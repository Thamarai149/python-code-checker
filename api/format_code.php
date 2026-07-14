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

$lines         = explode("\n", $code);
$formatted     = [];
$indent_level  = 0;
$indent_size   = 4;

foreach ($lines as $raw) {
    $line = rtrim($raw);          // preserve leading spaces intentionally stripped below
    $stripped = trim($line);

    if ($stripped === '') {
        $formatted[] = '';
        continue;
    }

    // Decrease indent BEFORE printing closing tokens
    if (
        str_starts_with($stripped, '}') ||
        str_starts_with($stripped, ']') ||
        str_starts_with($stripped, ')')
    ) {
        $indent_level = max(0, $indent_level - 1);
    }

    // Python: 'else:', 'elif', 'except:', 'finally:' reduce indent temporarily
    if ($language === 'python') {
        if (
            str_starts_with($stripped, 'else:') ||
            str_starts_with($stripped, 'elif ') ||
            str_starts_with($stripped, 'except') ||
            str_starts_with($stripped, 'finally:')
        ) {
            $effective_level = max(0, $indent_level - 1);
            $formatted[] = str_repeat(' ', $effective_level * $indent_size) . $stripped;

            // These lines still open a new block
            if (str_ends_with($stripped, ':')) {
                // indent stays as-is (we reduced by 1, so it stays balanced)
            }
            continue;
        }
    }

    $formatted[] = str_repeat(' ', $indent_level * $indent_size) . $stripped;

    // Increase indent AFTER printing opening tokens
    if (
        str_ends_with($stripped, '{') ||
        str_ends_with($stripped, '[') ||
        str_ends_with($stripped, '(')
    ) {
        $indent_level++;
    }

    // Python block openers (end with colon, not else/elif/except/finally handled above)
    if ($language === 'python' && str_ends_with($stripped, ':')) {
        $indent_level++;
    }
}

echo json_encode([
    'success' => true,
    'code'    => implode("\n", $formatted),
]);
