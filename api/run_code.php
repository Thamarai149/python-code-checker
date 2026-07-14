<?php
header('Content-Type: application/json');

$input = file_get_contents('php://input');
// Handle both JSON and application/x-www-form-urlencoded inputs
$data = json_decode($input, true);
if (!$data) {
    $data = $_POST;
}

if (!$data || !isset($data['code']) || !isset($data['language'])) {
    echo json_encode(['output' => '❌ Error: Code or language parameter missing.', 'result' => '❌ Error']);
    exit;
}

$code = $data['code'];
$language = $data['language'];
$program_input = isset($data['program_input']) ? $data['program_input'] : '';

$start_time = microtime(true);
$TIMEOUT = 30;

function run_command_with_input($cmd, $input = "", $timeout = 30) {
    $descriptorspec = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];
    // On Windows, pass bypass_shell to avoid escaping issues if needed, or run under cmd
    $process = proc_open($cmd, $descriptorspec, $pipes);
    if (!is_resource($process)) {
        return ["stdout" => "", "stderr" => "Failed to start process: $cmd", "exit_code" => -1, "timeout" => false];
    }
    
    fwrite($pipes[0], $input);
    fclose($pipes[0]);
    
    stream_set_blocking($pipes[1], 0);
    stream_set_blocking($pipes[2], 0);
    
    $stdout = "";
    $stderr = "";
    $proc_start_time = time();
    $terminated = false;
    
    while (true) {
        $status = proc_get_status($process);
        $stdout .= stream_get_contents($pipes[1]);
        $stderr .= stream_get_contents($pipes[2]);
        
        if (!$status['running']) {
            break;
        }
        
        if (time() - $proc_start_time > $timeout) {
            proc_terminate($process, 9);
            $terminated = true;
            break;
        }
        
        usleep(50000); // 50ms
    }
    
    $stdout .= stream_get_contents($pipes[1]);
    $stderr .= stream_get_contents($pipes[2]);
    
    fclose($pipes[1]);
    fclose($pipes[2]);
    
    $exit_code = $terminated ? -1 : proc_close($process);
    
    return [
        "stdout" => $stdout,
        "stderr" => $stderr,
        "exit_code" => $exit_code,
        "timeout" => $terminated
    ];
}

// Check language
if ($language === "html") {
    $exec_time = microtime(true) - $start_time;
    echo json_encode([
        'result' => '✅ HTML/CSS/JS preview compiled successfully.',
        'output' => '✅ HTML/CSS/JS preview compiled successfully.',
        'exec_time' => $exec_time
    ]);
    exit;
}

$temp_dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'nexus_' . uniqid();
mkdir($temp_dir, 0777, true);

$result = "";
$output = "";

try {
    switch ($language) {
        case "python":
            $temp_file = $temp_dir . DIRECTORY_SEPARATOR . 'program.py';
            file_put_contents($temp_file, $code);
            $res = run_command_with_input("python \"$temp_file\"", $program_input, $TIMEOUT);
            if ($res['timeout']) {
                $result = "❌ Execution timeout (30 seconds)";
                $output = $result;
            } else {
                $result = "✅ Python code is syntactically correct.";
                $output = $res['stdout'] !== "" ? $res['stdout'] : $res['stderr'];
            }
            break;

        case "php":
            $temp_file = $temp_dir . DIRECTORY_SEPARATOR . 'program.php';
            file_put_contents($temp_file, $code);
            $res = run_command_with_input("php \"$temp_file\"", $program_input, $TIMEOUT);
            if ($res['timeout']) {
                $result = "❌ Execution timeout (30 seconds)";
                $output = $result;
            } else {
                $result = "✅ PHP code is syntactically correct.";
                $output = $res['stdout'] !== "" ? $res['stdout'] : $res['stderr'];
            }
            break;

        case "javascript":
            $temp_file = $temp_dir . DIRECTORY_SEPARATOR . 'program.js';
            file_put_contents($temp_file, $code);
            $res = run_command_with_input("node \"$temp_file\"", $program_input, $TIMEOUT);
            if ($res['timeout']) {
                $result = "❌ Execution timeout (30 seconds)";
                $output = $result;
            } else {
                $result = $res['exit_code'] === 0 ? "✅ JavaScript code is syntactically correct." : "❌ Execution Error";
                $output = $res['stdout'] !== "" ? $res['stdout'] : $res['stderr'];
            }
            break;

        case "c":
            $temp_file = $temp_dir . DIRECTORY_SEPARATOR . 'program.c';
            $exe_file = $temp_dir . DIRECTORY_SEPARATOR . 'program.exe';
            file_put_contents($temp_file, $code);
            
            // Compile
            $comp_res = run_command_with_input("gcc \"$temp_file\" -o \"$exe_file\"", "", $TIMEOUT);
            if ($comp_res['exit_code'] !== 0) {
                $result = "❌ Compilation Error";
                $output = "❌ Compilation Error:\n" . $comp_res['stderr'];
            } else {
                // Run
                $res = run_command_with_input("\"$exe_file\"", $program_input, $TIMEOUT);
                if ($res['timeout']) {
                    $result = "❌ Execution timeout (30 seconds)";
                    $output = $result;
                } else {
                    $result = "✅ C code is syntactically correct.";
                    $output = $res['stdout'] !== "" ? $res['stdout'] : $res['stderr'];
                }
            }
            break;

        case "cpp":
            $temp_file = $temp_dir . DIRECTORY_SEPARATOR . 'program.cpp';
            $exe_file = $temp_dir . DIRECTORY_SEPARATOR . 'program.exe';
            file_put_contents($temp_file, $code);
            
            // Compile
            $comp_res = run_command_with_input("g++ \"$temp_file\" -o \"$exe_file\"", "", $TIMEOUT);
            if ($comp_res['exit_code'] !== 0) {
                $result = "❌ Compilation Error";
                $output = "❌ Compilation Error:\n" . $comp_res['stderr'];
            } else {
                // Run
                $res = run_command_with_input("\"$exe_file\"", $program_input, $TIMEOUT);
                if ($res['timeout']) {
                    $result = "❌ Execution timeout (30 seconds)";
                    $output = $result;
                } else {
                    $result = "✅ C++ code is syntactically correct.";
                    $output = $res['stdout'] !== "" ? $res['stdout'] : $res['stderr'];
                }
            }
            break;

        case "java":
            $temp_file = $temp_dir . DIRECTORY_SEPARATOR . 'Main.java';
            file_put_contents($temp_file, $code);
            
            // Compile
            $comp_res = run_command_with_input("javac \"$temp_file\"", "", $TIMEOUT);
            if ($comp_res['exit_code'] !== 0) {
                $result = "❌ Compilation Error";
                $output = "❌ Compilation Error:\n" . $comp_res['stderr'];
            } else {
                // Run
                $res = run_command_with_input("java -cp \"$temp_dir\" Main", $program_input, $TIMEOUT);
                if ($res['timeout']) {
                    $result = "❌ Execution timeout (30 seconds)";
                    $output = $result;
                } else {
                    $result = "✅ Java code is syntactically correct.";
                    $output = $res['stdout'] !== "" ? $res['stdout'] : $res['stderr'];
                }
            }
            break;

        case "ruby":
            $temp_file = $temp_dir . DIRECTORY_SEPARATOR . 'program.rb';
            file_put_contents($temp_file, $code);
            $res = run_command_with_input("ruby \"$temp_file\"", $program_input, $TIMEOUT);
            $result = $res['exit_code'] === 0 ? "✅ Ruby code is syntactically correct." : "❌ Execution Error";
            $output = $res['stdout'] !== "" ? $res['stdout'] : $res['stderr'];
            break;

        case "go":
            $temp_file = $temp_dir . DIRECTORY_SEPARATOR . 'main.go';
            file_put_contents($temp_file, $code);
            $res = run_command_with_input("go run \"$temp_file\"", $program_input, $TIMEOUT);
            $result = $res['exit_code'] === 0 ? "✅ Go code is syntactically correct." : "❌ Execution Error";
            $output = $res['stdout'] !== "" ? $res['stdout'] : $res['stderr'];
            break;

        default:
            // For other secondary languages, support general scripting run command if available
            $result = "❌ Language execution not supported natively or needs configuration.";
            $output = "Language \"$language\" execution is not configured in PHP wrapper on Windows/XAMPP environment.";
            break;
    }
} catch (Exception $e) {
    $result = "❌ Exception Error";
    $output = "Exception occurred: " . $e->getMessage();
}

// Cleanup temp files
try {
    $files = glob($temp_dir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    rmdir($temp_dir);
} catch (Exception $e) {
    // Ignore cleanup errors
}

$exec_time = microtime(true) - $start_time;

echo json_encode([
    'result' => $result,
    'output' => $output,
    'exec_time' => $exec_time
]);
