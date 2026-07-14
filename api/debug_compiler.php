<?php
// TEMPORARY DEBUG FILE - DELETE AFTER FIXING
header('Content-Type: text/plain');

$gcc = 'C:\\ProgramData\\mingw64\\mingw64\\bin\\gcc.exe';

$temp_dir = realpath(sys_get_temp_dir()) . '\\nexus_debug_' . uniqid();
mkdir($temp_dir, 0777, true);
$src = $temp_dir . '\\test.c';
$bin = $temp_dir . '\\test.exe';

file_put_contents($src, '#include <stdio.h>' . "\n" . 'int main(){printf("Hello C!\\n");return 0;}');

echo "Temp dir: $temp_dir\n";
echo "GCC exists: " . (file_exists($gcc) ? "YES" : "NO") . "\n";
echo "Apache user: " . get_current_user() . " / " . exec('whoami') . "\n\n";

// Build environment with mingw64 on PATH
$env = [];
foreach ($_SERVER as $k => $v) { if (is_string($v)) $env[$k] = $v; }
$extra = 'C:\\ProgramData\\mingw64\\mingw64\\bin;C:\\Windows\\System32;C:\\Windows';
$env['PATH'] = $extra . ';' . (isset($env['PATH']) ? $env['PATH'] : '');
$env['Path'] = $env['PATH'];

$descriptorspec = [0=>['pipe','r'], 1=>['pipe','w'], 2=>['pipe','w']];
$cmd = [$gcc, $src, '-o', $bin, '-lm'];
echo "CMD: " . implode(' ', $cmd) . "\n";

$process = proc_open($cmd, $descriptorspec, $pipes, null, $env);
if (!is_resource($process)) {
    echo "proc_open FAILED\n";
} else {
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]); fclose($pipes[2]);
    $exit = proc_close($process);
    echo "Exit: $exit\nSTDOUT: '$stdout'\nSTDERR: '$stderr'\n";
    echo "Binary exists: " . (file_exists($bin) ? "YES" : "NO") . "\n\n";
}

if (file_exists($bin)) {
    echo "--- Running binary ---\n";
    $p2 = proc_open([$bin], $descriptorspec, $p2ipes, null, $env);
    if (is_resource($p2)) {
        fclose($p2ipes[0]);
        echo "Output: '" . stream_get_contents($p2ipes[1]) . "'\n";
        echo "Errors: '" . stream_get_contents($p2ipes[2]) . "'\n";
        fclose($p2ipes[1]); fclose($p2ipes[2]);
        echo "Exit: " . proc_close($p2) . "\n";
    }
}

@unlink($src); @unlink($bin); @rmdir($temp_dir);
