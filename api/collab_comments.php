<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

$db_file        = __DIR__ . '/../data/comments.json';
$comments_store = [];

if (file_exists($db_file)) {
    $raw = file_get_contents($db_file);
    $comments_store = json_decode($raw, true) ?: [];
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // --- Add a new comment ---
    $input = file_get_contents('php://input');
    $data  = json_decode($input, true);

    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'Invalid JSON input.']);
        exit;
    }

    $share_id = isset($data['share_id']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $data['share_id']) : 'default';
    $author   = isset($data['author'])   ? trim(htmlspecialchars($data['author'], ENT_QUOTES, 'UTF-8'))   : 'Anonymous Developer';
    $text     = isset($data['text'])     ? trim($data['text'])     : '';

    if ($author === '') $author = 'Anonymous Developer';

    if ($text === '') {
        echo json_encode(['success' => false, 'error' => 'Comment text cannot be empty.']);
        exit;
    }

    // Limit comment text length
    if (strlen($text) > 1000) {
        echo json_encode(['success' => false, 'error' => 'Comment is too long (max 1000 characters).']);
        exit;
    }

    $comment = [
        'id'        => substr(md5(uniqid('', true)), 0, 8),
        'author'    => $author,
        'text'      => htmlspecialchars($text, ENT_QUOTES, 'UTF-8'),
        'timestamp' => date('h:i A'),
        'date'      => date('Y-m-d'),
    ];

    if (!isset($comments_store[$share_id])) {
        $comments_store[$share_id] = [];
    }

    $comments_store[$share_id][] = $comment;

    // Keep only last 200 comments per session
    if (count($comments_store[$share_id]) > 200) {
        $comments_store[$share_id] = array_slice($comments_store[$share_id], -200);
    }

    // Ensure data directory exists
    $data_dir = dirname($db_file);
    if (!is_dir($data_dir)) {
        mkdir($data_dir, 0777, true);
    }

    file_put_contents($db_file, json_encode($comments_store, JSON_PRETTY_PRINT));

    echo json_encode(['success' => true, 'comment' => $comment]);

} elseif ($method === 'GET') {
    // --- Fetch comments for a session ---
    $share_id = isset($_GET['share_id']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['share_id']) : 'default';

    if (isset($comments_store[$share_id]) && count($comments_store[$share_id]) > 0) {
        $comments = $comments_store[$share_id];
    } else {
        // Default welcome message for new sessions
        $comments = [
            [
                'id'        => 'system-0',
                'author'    => '🤖 Nexus System',
                'text'      => 'Welcome to the live collaboration session! Add notes, share findings, or discuss code changes here.',
                'timestamp' => 'Just now',
                'date'      => date('Y-m-d'),
            ]
        ];
    }

    echo json_encode(['success' => true, 'comments' => $comments]);

} elseif ($method === 'DELETE') {
    // --- Clear all comments for a session ---
    $input    = file_get_contents('php://input');
    $data     = json_decode($input, true);
    $share_id = isset($data['share_id']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $data['share_id']) : 'default';

    if (isset($comments_store[$share_id])) {
        unset($comments_store[$share_id]);
        file_put_contents($db_file, json_encode($comments_store, JSON_PRETTY_PRINT));
    }

    echo json_encode(['success' => true, 'message' => 'Comments cleared.']);

} else {
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
}
