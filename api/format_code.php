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

$lines = explode("\n", $code);
$formatted_lines = [];
$indent_level = 0;
$indent_size = 4;

foreach ($lines as $line) {
    $stripped = trim($line);
    if ($stripped === '') {
        $formatted_lines[] = '';
        continue;
    }

    // Decrease indent if line starts with closing brace
    if (
        str_starts_with($stripped, '}') || 
        str_starts_with($stripped, ']') || 
        str_starts_with($stripped, ')')
    ) {
        $indent_level = max(0, $indent_level - 1);
    }

    $formatted_lines[] = str_repeat(' ', $indent_level * $indent_size) . $stripped;

    // Increase indent if line ends with opening brace or colon (Python)
    if (
        str_ends_with($stripped, '{') || 
        str_ends_with($stripped, '[') || 
        str_ends_with($stripped, '(') || 
        ($language === 'python' && str_ends_with($stripped, ':'))
    ) {
        $indent_level++;
    }
}

echo json_encode(['success' => true, 'code' => implode("\n", $formatted_lines)]);
