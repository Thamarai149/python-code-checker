<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

$db_file  = __DIR__ . '/../data/snippets.json';
$snippets = [];

if (file_exists($db_file)) {
    $content  = file_get_contents($db_file);
    $snippets = json_decode($content, true) ?: [];
}

// Filter out deleted snippets before returning
$active = array_values(array_filter($snippets, fn($s) => empty($s['is_deleted'])));

echo json_encode([
    'success'  => true,
    'snippets' => $active,
    'total'    => count($active),
]);
