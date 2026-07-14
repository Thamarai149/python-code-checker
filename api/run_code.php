<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$input = file_get_contents('php://input');
$data = json_decode($input, true);
if (!$data) {
    $data = $_POST;
}

if (!$data || !isset($data['code']) || !isset($data['language'])) {
    echo json_encode([
        'output' => '❌ Error: Code or language parameter missing.',
        'result' => '❌ Error',
        'exec_time' => 0
    ]);
    exit;
}

$code           = $data['code'];
$language       = strtolower(trim($data['language']));
$program_input  = isset($data['program_input']) ? $data['program_input'] : '';

$start_time = microtime(true);
$TIMEOUT    = 30;

/**
 * Run a command with stdin support and timeout.
 * $cmd can be a string (shell command) or an array [binary, arg1, arg2, ...]
 * Array form bypasses the shell entirely — no quoting issues on Windows.
 */
function run_cmd($cmd, $stdin = '', $timeout = 30) {
    $descriptorspec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($cmd, $descriptorspec, $pipes);

    if (!is_resource($process)) {
        $label = is_array($cmd) ? implode(' ', $cmd) : $cmd;
        return [
            'stdout'    => '',
            'stderr'    => "Failed to start process: $label",
            'exit_code' => -1,
            'timeout'   => false,
        ];
    }

    fwrite($pipes[0], $stdin);
    fclose($pipes[0]);

    stream_set_blocking($pipes[1], 0);
    stream_set_blocking($pipes[2], 0);

    $stdout     = '';
    $stderr     = '';
    $start      = time();
    $terminated = false;

    while (true) {
        $status  = proc_get_status($process);
        $stdout .= stream_get_contents($pipes[1]);
        $stderr .= stream_get_contents($pipes[2]);

        if (!$status['running']) {
            break;
        }

        if ((time() - $start) >= $timeout) {
            proc_terminate($process, 9);
            $terminated = true;
            break;
        }

        usleep(50000); // 50 ms
    }

    // Drain any remaining output
    $stdout .= stream_get_contents($pipes[1]);
    $stderr .= stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    $exit_code = $terminated ? -1 : proc_close($process);

    return [
        'stdout'    => $stdout,
        'stderr'    => $stderr,
        'exit_code' => $exit_code,
        'timeout'   => $terminated,
    ];
}

/**
 * Detect Python binary (python3 or python).
 */
function python_bin() {
    $check = shell_exec('where python3 2>nul');
    if (trim($check)) return 'python3';
    return 'python';
}

/**
 * Detect Node.js binary.
 */
function node_bin() {
    $check = shell_exec('where node 2>nul');
    if (trim($check)) return 'node';
    return 'node';
}

/**
 * Detect the best available GCC binary.
 * Prefers the 64-bit mingw64 GCC over the legacy 32-bit MinGW,
 * which fails with "invalid instruction suffix for push" on modern code.
 */
function gcc_bin() {
    // Explicit 64-bit mingw64 path first (avoids old MinGW on PATH)
    $candidates = [
        'C:\\ProgramData\\mingw64\\mingw64\\bin\\gcc.exe',
        'C:\\msys64\\mingw64\\bin\\gcc.exe',
        'C:\\msys64\\ucrt64\\bin\\gcc.exe',
    ];
    foreach ($candidates as $path) {
        if (file_exists($path)) return '"' . $path . '"';
    }
    // Fallback: whatever is on PATH (works fine inside Docker/Linux)
    return 'gcc';
}

/**
 * Detect the best available G++ binary.
 */
function gpp_bin() {
    $candidates = [
        'C:\\ProgramData\\mingw64\\mingw64\\bin\\g++.exe',
        'C:\\msys64\\mingw64\\bin\\g++.exe',
        'C:\\msys64\\ucrt64\\bin\\g++.exe',
    ];
    foreach ($candidates as $path) {
        if (file_exists($path)) return '"' . $path . '"';
    }
    return 'g++';
}

// ----- HTML/CSS preview - no execution needed -----
if ($language === 'html') {
    $exec_time = microtime(true) - $start_time;
    echo json_encode([
        'result'    => '✅ HTML/CSS/JS rendered in preview panel.',
        'output'    => '✅ HTML/CSS/JS rendered in preview panel.',
        'exec_time' => round($exec_time, 4),
    ]);
    exit;
}

// ----- Create isolated temp workspace -----
// sys_get_temp_dir() can return 8.3 short paths (e.g. THAMAR~1) on Windows
// which GCC/G++ cannot handle. Resolve to the full long path first.
function long_temp_dir() {
    $raw = sys_get_temp_dir();
    // On Windows, use realpath() to expand 8.3 short names to full paths
    if (DIRECTORY_SEPARATOR === '\\') {
        $resolved = realpath($raw);
        if ($resolved) return $resolved;
    }
    return $raw;
}

$temp_dir = long_temp_dir() . DIRECTORY_SEPARATOR . 'nexus_' . uniqid('', true);
if (!mkdir($temp_dir, 0777, true)) {
    echo json_encode([
        'result'    => '❌ Internal Error: Cannot create temp directory.',
        'output'    => '❌ Server error. Please try again.',
        'exec_time' => 0,
    ]);
    exit;
}

$result = '';
$output = '';

try {
    switch ($language) {

        // ---- Python ----
        case 'python':
            $file = $temp_dir . DIRECTORY_SEPARATOR . 'program.py';
            file_put_contents($file, $code);
            $py = python_bin();
            $res = run_cmd("$py \"$file\"", $program_input, $TIMEOUT);

            if ($res['timeout']) {
                $result = '⏱️ Execution timeout (30s limit reached).';
                $output = $result;
            } else {
                $result = $res['exit_code'] === 0
                    ? '✅ Python executed successfully.'
                    : '❌ Python Runtime Error';
                $output = $res['stdout'] !== '' ? $res['stdout'] : $res['stderr'];
            }
            break;

        // ---- JavaScript (Node.js) ----
        case 'javascript':
        case 'js':
            $file = $temp_dir . DIRECTORY_SEPARATOR . 'program.js';
            file_put_contents($file, $code);
            $nd = node_bin();
            $res = run_cmd("$nd \"$file\"", $program_input, $TIMEOUT);

            if ($res['timeout']) {
                $result = '⏱️ Execution timeout (30s limit reached).';
                $output = $result;
            } else {
                $result = $res['exit_code'] === 0
                    ? '✅ JavaScript executed successfully.'
                    : '❌ JavaScript Runtime Error';
                $output = $res['stdout'] !== '' ? $res['stdout'] : $res['stderr'];
            }
            break;

        // ---- PHP ----
        case 'php':
            $file = $temp_dir . DIRECTORY_SEPARATOR . 'program.php';
            file_put_contents($file, $code);
            $res = run_cmd("php \"$file\"", $program_input, $TIMEOUT);

            if ($res['timeout']) {
                $result = '⏱️ Execution timeout (30s limit reached).';
                $output = $result;
            } else {
                $result = $res['exit_code'] === 0
                    ? '✅ PHP executed successfully.'
                    : '❌ PHP Runtime Error';
                $output = $res['stdout'] !== '' ? $res['stdout'] : $res['stderr'];
            }
            break;

        // ---- C ----
        case 'c':
            $src  = $temp_dir . DIRECTORY_SEPARATOR . 'program.c';
            $bin  = $temp_dir . DIRECTORY_SEPARATOR . 'program_out';
            file_put_contents($src, $code);

            $comp = run_cmd(gcc_bin() . " \"$src\" -o \"$bin\" -lm", '', $TIMEOUT);
            if ($comp['exit_code'] !== 0) {
                $result = '❌ Compilation Error';
                $output = "❌ Compilation Error:\n" . $comp['stderr'];
            } else {
                $res = run_cmd("\"$bin\"", $program_input, $TIMEOUT);
                if ($res['timeout']) {
                    $result = '⏱️ Execution timeout (30s limit reached).';
                    $output = $result;
                } else {
                    $result = $res['exit_code'] === 0
                        ? '✅ C program executed successfully.'
                        : '❌ C Runtime Error';
                    $output = $res['stdout'] !== '' ? $res['stdout'] : $res['stderr'];
                }
            }
            break;

        // ---- C++ ----
        case 'cpp':
        case 'c++':
            $src  = $temp_dir . DIRECTORY_SEPARATOR . 'program.cpp';
            $bin  = $temp_dir . DIRECTORY_SEPARATOR . 'program_out';
            file_put_contents($src, $code);

            $comp = run_cmd(gpp_bin() . " \"$src\" -o \"$bin\" -std=c++17 -lm", '', $TIMEOUT);
            if ($comp['exit_code'] !== 0) {
                $result = '❌ Compilation Error';
                $output = "❌ Compilation Error:\n" . $comp['stderr'];
            } else {
                $res = run_cmd("\"$bin\"", $program_input, $TIMEOUT);
                if ($res['timeout']) {
                    $result = '⏱️ Execution timeout (30s limit reached).';
                    $output = $result;
                } else {
                    $result = $res['exit_code'] === 0
                        ? '✅ C++ program executed successfully.'
                        : '❌ C++ Runtime Error';
                    $output = $res['stdout'] !== '' ? $res['stdout'] : $res['stderr'];
                }
            }
            break;

        // ---- Java ----
        case 'java':
            $src = $temp_dir . DIRECTORY_SEPARATOR . 'Main.java';
            file_put_contents($src, $code);

            $comp = run_cmd("javac \"$src\"", '', $TIMEOUT);
            if ($comp['exit_code'] !== 0) {
                $result = '❌ Compilation Error';
                $output = "❌ Compilation Error:\n" . $comp['stderr'];
            } else {
                $res = run_cmd("java -cp \"$temp_dir\" Main", $program_input, $TIMEOUT);
                if ($res['timeout']) {
                    $result = '⏱️ Execution timeout (30s limit reached).';
                    $output = $result;
                } else {
                    $result = $res['exit_code'] === 0
                        ? '✅ Java program executed successfully.'
                        : '❌ Java Runtime Error';
                    $output = $res['stdout'] !== '' ? $res['stdout'] : $res['stderr'];
                }
            }
            break;

        // ---- Ruby ----
        case 'ruby':
            $file = $temp_dir . DIRECTORY_SEPARATOR . 'program.rb';
            file_put_contents($file, $code);
            $res = run_cmd("ruby \"$file\"", $program_input, $TIMEOUT);
            $result = $res['exit_code'] === 0 ? '✅ Ruby executed successfully.' : '❌ Ruby Runtime Error';
            $output = $res['stdout'] !== '' ? $res['stdout'] : $res['stderr'];
            break;

        // ---- Go ----
        case 'go':
            $file = $temp_dir . DIRECTORY_SEPARATOR . 'main.go';
            file_put_contents($file, $code);
            $res = run_cmd("go run \"$file\"", $program_input, $TIMEOUT);
            $result = $res['exit_code'] === 0 ? '✅ Go executed successfully.' : '❌ Go Runtime Error';
            $output = $res['stdout'] !== '' ? $res['stdout'] : $res['stderr'];
            break;

        // ---- Bash ----
        case 'bash':
        case 'sh':
            $file = $temp_dir . DIRECTORY_SEPARATOR . 'program.sh';
            file_put_contents($file, $code);
            $res = run_cmd("bash \"$file\"", $program_input, $TIMEOUT);
            $result = $res['exit_code'] === 0 ? '✅ Bash script executed successfully.' : '❌ Bash Runtime Error';
            $output = $res['stdout'] !== '' ? $res['stdout'] : $res['stderr'];
            break;

        // ---- Unsupported ----
        default:
            $result = "⚠️ Language \"$language\" is not supported by this execution engine.";
            $output = $result;
            break;
    }

} catch (Exception $e) {
    $result = '❌ Internal Server Exception';
    $output = 'Exception: ' . $e->getMessage();
}

// ----- Cleanup temp workspace -----
array_map('unlink', glob($temp_dir . '/*') ?: []);
@rmdir($temp_dir);

$exec_time = microtime(true) - $start_time;

echo json_encode([
    'result'    => $result,
    'output'    => $output !== '' ? $output : '(No output)',
    'exec_time' => round($exec_time, 4),
]);
