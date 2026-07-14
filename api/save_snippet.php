<?php
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
    exit;
}

$name = isset($data['name']) ? $data['name'] : 'Untitled';
$code = isset($data['code']) ? $data['code'] : '';
$language = isset($data['language']) ? $data['language'] : 'python';
$folder = isset($data['folder']) ? $data['folder'] : 'root';

$db_file = __DIR__ . '/../data/snippets.json';
$snippets = [];

if (file_exists($db_file)) {
    $content = file_get_contents($db_file);
    $snippets = json_decode($content, true) ?: [];
}

$snippet_id = substr(md5(uniqid(rand(), true)), 0, 8);
$new_snippet = [
    'id' => $snippet_id,
    'name' => $name,
    'code' => $code,
    'language' => $language,
    'folder' => $folder,
    'is_deleted' => false,
    'timestamp' => date('Y-m-d H:i:s')
];

array_unshift($snippets, $new_snippet);

// Keep last 100 snippets
if (count($snippets) > 100) {
    $snippets = array_slice($snippets, 0, 100);
}

file_put_contents($db_file, json_encode($snippets, JSON_PRETTY_PRINT));

echo json_encode(['success' => true, 'id' => $snippet_id]);
