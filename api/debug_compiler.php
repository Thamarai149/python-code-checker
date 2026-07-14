<?php
// TEMPORARY DEBUG FILE - DELETE AFTER FIXING
header('Content-Type: text/plain');

$gcc = 'C:\\ProgramData\\mingw64\\mingw64\\bin\\gcc.exe';

// Write a test C file
$temp_dir = sys_get_temp_dir() . '\\nexus_debug_' . uniqid();
mkdir($temp_dir, 0777, true);
$src = $temp_dir . '\\test.c';
$bin = $temp_dir . '\\test.exe';

file_put_contents($src, '#include <stdio.h>' . "\n" . 'int main(){printf("Hello C!\\n");return 0;}');

echo "Temp dir: $temp_dir\n";
echo "Source: $src\n";
echo "Binary: $bin\n";
echo "GCC exists: " . (file_exists($gcc) ? "YES" : "NO") . "\n\n";

// Method 1: proc_open with array (most reliable on Windows)
$cmd_parts = [$gcc, $src, '-o', $bin, '-lm'];
echo "CMD array: " . implode(' ', $cmd_parts) . "\n";

$descriptorspec = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
];

$process = proc_open($cmd_parts, $descriptorspec, $pipes);
if (!is_resource($process)) {
    echo "proc_open FAILED\n";
} else {
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exit = proc_close($process);
    echo "Exit code: $exit\n";
    echo "STDOUT: '$stdout'\n";
    echo "STDERR: '$stderr'\n";
    echo "Binary exists: " . (file_exists($bin) ? "YES" : "NO") . "\n\n";
}

// Run the binary if it was created
if (file_exists($bin)) {
    echo "--- Running binary ---\n";
    $process2 = proc_open([$bin], $descriptorspec, $pipes2);
    if (is_resource($process2)) {
        fclose($pipes2[0]);
        $out = stream_get_contents($pipes2[1]);
        $err = stream_get_contents($pipes2[2]);
        fclose($pipes2[1]);
        fclose($pipes2[2]);
        $exit2 = proc_close($process2);
        echo "Output: '$out'\n";
        echo "Errors: '$err'\n";
        echo "Exit: $exit2\n";
    }
}

// Cleanup
@unlink($src); @unlink($bin); @rmdir($temp_dir);
