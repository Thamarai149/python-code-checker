<?php
header('Content-Type: application/json');

$db_file = __DIR__ . '/../data/snippets.json';
$snippets = [];

if (file_exists($db_file)) {
    $content = file_get_contents($db_file);
    $snippets = json_decode($content, true) ?: [];
}

echo json_encode(['snippets' => $snippets]);
