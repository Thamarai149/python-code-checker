<?php
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['messages']) || empty($data['messages'])) {
    echo json_encode(['success' => false, 'error' => 'No input query found']);
    exit;
}

$messages = $data['messages'];
$code = isset($data['code']) ? $data['code'] : '';
$language = isset($data['language']) ? $data['language'] : 'python';

$user_msg = strtolower($messages[count($messages) - 1]['content']);

$reply = '';
if (str_contains($user_msg, 'help') || str_contains($user_msg, 'how')) {
    $reply = "To program in " . strtoupper($language) . ", write your instructions in the center workspace panel, adjust editor controls via settings, and click **Run**.";
} elseif (str_contains($user_msg, 'bug') || str_contains($user_msg, 'error') || str_contains($user_msg, 'fix')) {
    $reply = "I can review potential syntax bugs. Try clicking the **AI Bug Fix** action in the right assistant panel!";
} elseif (str_contains($user_msg, 'explain')) {
    $reply = "You can explain this entire workspace instantly by clicking the **AI Explain Code** button. Let me know if you have questions about specific lines!";
} elseif (str_contains($user_msg, 'optimize') || str_contains($user_msg, 'fast')) {
    $reply = "To speed up execution, try using in-place assignments, avoid nested loop lookups, and use built-in algorithms.";
} else {
    $reply = "I'm your AI Workspace Assistant. I'm currently monitoring your " . strtoupper($language) . " code. Try selecting one of the rapid AI actions (Review, Fix, Explain, Optimize) or ask me details about syntax!";
}

echo json_encode([
    'success' => true,
    'reply' => $reply,
    'timestamp' => date('h:i A')
]);
