<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

$input = file_get_contents('php://input');
$data  = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid or missing JSON body.']);
    exit;
}

$name     = isset($data['name'])     ? trim($data['name'])     : 'Untitled';
$code     = isset($data['code'])     ? $data['code']           : '';
$language = isset($data['language']) ? strtolower(trim($data['language'])) : 'python';
$folder   = isset($data['folder'])   ? trim($data['folder'])   : 'root';

if ($name === '') $name = 'Untitled';

$db_file  = __DIR__ . '/../data/snippets.json';
$snippets = [];

if (file_exists($db_file)) {
    $content  = file_get_contents($db_file);
    $snippets = json_decode($content, true) ?: [];
}

// Handle DELETE (soft-delete by ID)
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $del_id = isset($data['id']) ? $data['id'] : '';
    if (!$del_id) {
        echo json_encode(['success' => false, 'error' => 'No snippet ID provided for deletion.']);
        exit;
    }
    $found = false;
    foreach ($snippets as &$snippet) {
        if ($snippet['id'] === $del_id) {
            $snippet['is_deleted'] = true;
            $found = true;
            break;
        }
    }
    unset($snippet);
    if (!$found) {
        echo json_encode(['success' => false, 'error' => 'Snippet not found.']);
        exit;
    }
    file_put_contents($db_file, json_encode($snippets, JSON_PRETTY_PRINT));
    echo json_encode(['success' => true, 'message' => 'Snippet deleted.']);
    exit;
}

// Handle POST (create new snippet)
$snippet_id  = substr(md5(uniqid('', true)), 0, 8);
$new_snippet = [
    'id'         => $snippet_id,
    'name'       => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
    'code'       => $code,
    'language'   => $language,
    'folder'     => $folder,
    'is_deleted' => false,
    'timestamp'  => date('Y-m-d H:i:s'),
    'created_at' => time(),
];

// Prepend new snippet (newest first)
array_unshift($snippets, $new_snippet);

// Cap at 100 stored snippets
if (count($snippets) > 100) {
    $snippets = array_slice($snippets, 0, 100);
}

// Ensure the data directory exists
$data_dir = dirname($db_file);
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0777, true);
}

if (!file_put_contents($db_file, json_encode($snippets, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => false, 'error' => 'Failed to write snippet to disk.']);
    exit;
}

echo json_encode([
    'success' => true,
    'id'      => $snippet_id,
    'snippet' => $new_snippet,
]);
