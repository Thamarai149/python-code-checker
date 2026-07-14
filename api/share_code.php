<?php
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['code'])) {
    echo json_encode(['success' => false, 'error' => 'No code provided']);
    exit;
}

$code = $data['code'];
$language = isset($data['language']) ? $data['language'] : 'python';

$share_id = substr(md5($code), 0, 10);

$shared_dir = __DIR__ . '/../shared_codes';
if (!is_dir($shared_dir)) {
    mkdir($shared_dir, 0777, true);
}

$share_path = $shared_dir . '/' . $share_id . '.json';
$share_data = [
    'code' => $code,
    'language' => $language,
    'timestamp' => date('Y-m-d H:i:s')
];

file_put_contents($share_path, json_encode($share_data, JSON_PRETTY_PRINT));

// Build external share URL dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];
// We need to replace "/api/share_code.php" with "/index.php?share_id=$share_id"
$base_path = dirname(dirname($uri));
// Ensure Windows slashes are normalized for URL path
$base_path = str_replace('\\', '/', $base_path);
if ($base_path === '/') {
    $base_path = '';
}
$share_url = $protocol . '://' . $host . $base_path . '/index.php?share_id=' . $share_id;

echo json_encode(['success' => true, 'url' => $share_url]);
