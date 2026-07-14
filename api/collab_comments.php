<?php
header('Content-Type: application/json');

$db_file = __DIR__ . '/../data/comments.json';
$comments_store = [];

if (file_exists($db_file)) {
    $content = file_get_contents($db_file);
    $comments_store = json_decode($content, true) ?: [];
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
        exit;
    }
    
    $share_id = isset($data['share_id']) ? $data['share_id'] : 'default';
    $author = isset($data['author']) ? $data['author'] : 'Anonymous Developer';
    $text = isset($data['text']) ? $data['text'] : '';
    
    if (trim($text) === '') {
        echo json_encode(['success' => false, 'error' => 'Empty comment']);
        exit;
    }
    
    $comment = [
        'author' => $author,
        'text' => $text,
        'timestamp' => date('h:i A')
    ];
    
    if (!isset($comments_store[$share_id])) {
        $comments_store[$share_id] = [];
    }
    
    $comments_store[$share_id][] = $comment;
    
    file_put_contents($db_file, json_encode($comments_store, JSON_PRETTY_PRINT));
    
    echo json_encode(['success' => true, 'comment' => $comment]);
} else {
    $share_id = isset($_GET['share_id']) ? $_GET['share_id'] : 'default';
    
    if (isset($comments_store[$share_id])) {
        $comments = $comments_store[$share_id];
    } else {
        $comments = [
            [
                'author' => 'System AI',
                'text' => 'Welcome to live session comments! Add notes or review changes.',
                'timestamp' => 'Now'
            ]
        ];
    }
    
    echo json_encode(['success' => true, 'comments' => $comments]);
}
