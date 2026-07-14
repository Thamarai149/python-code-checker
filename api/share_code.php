<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

$input = file_get_contents('php://input');
$data  = json_decode($input, true);

if (!$data || !isset($data['code'])) {
    echo json_encode(['success' => false, 'error' => 'No code provided']);
    exit;
}

$code     = $data['code'];
$language = isset($data['language']) ? strtolower(trim($data['language'])) : 'python';

// Generate a unique but deterministic share ID based on code content
$share_id = substr(md5($code . $language), 0, 12);

// Ensure shared_codes directory exists
$shared_dir = __DIR__ . '/../shared_codes';
if (!is_dir($shared_dir)) {
    mkdir($shared_dir, 0777, true);
}

// Save the shared code as JSON
$share_path = $shared_dir . '/' . $share_id . '.json';
$share_data = [
    'code'      => $code,
    'language'  => $language,
    'timestamp' => date('Y-m-d H:i:s'),
    'created'   => time(),
];

if (!file_put_contents($share_path, json_encode($share_data, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => false, 'error' => 'Failed to save shared code. Check server write permissions.']);
    exit;
}

// Build the public share URL dynamically
$protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
$request_uri = $_SERVER['REQUEST_URI'] ?? '/api/share_code.php';

// Strip /api/share_code.php from the end to get base path
$base_path = preg_replace('#/api/share_code\.php.*$#', '', $request_uri);
$base_path = rtrim($base_path, '/');

$share_url = $protocol . '://' . $host . $base_path . '/index.php?share_id=' . $share_id;

echo json_encode([
    'success'  => true,
    'url'      => $share_url,
    'share_id' => $share_id,
]);
