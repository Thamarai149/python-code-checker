<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'error' => 'No file provided.']);
    exit;
}

$file = $_FILES['file'];

// Check for upload errors
$upload_errors = [
    UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit.',
    UPLOAD_ERR_FORM_SIZE  => 'File exceeds form upload limit.',
    UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
    UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
    UPLOAD_ERR_NO_TMP_DIR => 'Missing server temp directory.',
    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
    UPLOAD_ERR_EXTENSION  => 'Upload blocked by server extension.',
];

if ($file['error'] !== UPLOAD_ERR_OK) {
    $msg = $upload_errors[$file['error']] ?? 'Unknown upload error (code: ' . $file['error'] . ')';
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

// File size cap: 2MB
if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(['success' => false, 'error' => 'File too large. Maximum allowed size is 2MB.']);
    exit;
}

// Allowed file extensions
$allowed_extensions = [
    'py', 'js', 'ts', 'php', 'c', 'cpp', 'h', 'java',
    'rb', 'go', 'sh', 'bash', 'rs', 'lua', 'pl', 'r',
    'kt', 'swift', 'dart', 'cs', 'fs', 'vb', 'hs', 'ml',
    'f90', 'f95', 'cob', 'pas', 'asm', 's', 'json', 'xml',
    'yaml', 'yml', 'toml', 'md', 'txt', 'html', 'css', 'sql',
];

$original_name = $file['name'] ?? 'upload';
$ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

if (!in_array($ext, $allowed_extensions, true)) {
    echo json_encode([
        'success' => false,
        'error'   => "File type .$ext is not allowed. Supported: " . implode(', ', $allowed_extensions),
    ]);
    exit;
}

// Read the file contents
$code = file_get_contents($file['tmp_name']);

if ($code === false) {
    echo json_encode(['success' => false, 'error' => 'Failed to read uploaded file.']);
    exit;
}

// Detect language from extension
$ext_to_lang = [
    'py'    => 'python',
    'js'    => 'javascript',
    'ts'    => 'typescript',
    'php'   => 'php',
    'c'     => 'c',
    'cpp'   => 'cpp',
    'h'     => 'c',
    'java'  => 'java',
    'rb'    => 'ruby',
    'go'    => 'go',
    'sh'    => 'bash',
    'bash'  => 'bash',
    'rs'    => 'rust',
    'lua'   => 'lua',
    'pl'    => 'perl',
    'r'     => 'r',
    'kt'    => 'kotlin',
    'swift' => 'swift',
    'dart'  => 'dart',
    'cs'    => 'csharp',
    'html'  => 'html',
    'css'   => 'html',
    'sql'   => 'sql',
];

$detected_language = $ext_to_lang[$ext] ?? 'python';

echo json_encode([
    'success'   => true,
    'code'      => $code,
    'filename'  => $original_name,
    'extension' => $ext,
    'language'  => $detected_language,
    'size'      => $file['size'],
]);
