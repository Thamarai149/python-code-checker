<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

$input = file_get_contents('php://input');
$data  = json_decode($input, true);

if (!$data || !isset($data['messages']) || empty($data['messages'])) {
    echo json_encode(['success' => false, 'error' => 'No messages provided.']);
    exit;
}

$messages = $data['messages'];
$code     = isset($data['code'])     ? trim($data['code'])     : '';
$language = isset($data['language']) ? strtolower(trim($data['language'])) : 'python';

// Get the latest user message
$last_msg  = end($messages);
$user_msg  = isset($last_msg['content']) ? strtolower(trim($last_msg['content'])) : '';
$lang_upper = strtoupper($language);

// Build context info about the current code
$line_count = $code ? count(explode("\n", $code)) : 0;
$code_info  = $line_count > 0 ? "Your current $lang_upper file has $line_count lines." : "";

// Smart pattern-matching response engine
$reply = '';

if (preg_match('/\b(hello|hi|hey|hola|greet)\b/', $user_msg)) {
    $reply = "👋 Hello! I'm **Nexus AI**, your coding assistant. $code_info Ask me anything about your code, syntax, algorithms, or debugging!";

} elseif (preg_match('/\b(help|how (do i|to|can i)|what (is|are|does))\b/', $user_msg)) {
    $reply = "🛠️ **How to use Nexus Studio:**\n\n"
           . "1. Write your **$lang_upper** code in the center workspace editor.\n"
           . "2. Click **▶ Run** to execute it — output appears in the console below.\n"
           . "3. Use **AI Actions** (Explain, Optimize, Fix, Review) on the right panel.\n"
           . "4. **Share** your code via URL, or **Save** snippets to your library.\n"
           . "5. Switch languages via the toolbar dropdown.";

} elseif (preg_match('/\b(bug|error|exception|crash|fail|fix|debug|traceback|undefined)\b/', $user_msg)) {
    $reply = "🔍 **Debugging Tips for $lang_upper:**\n\n"
           . "- Click **🔧 AI Bug Fix** in the right panel for instant syntax analysis.\n"
           . "- Check for common issues: missing colons (Python), missing semicolons (C/Java/JS), or wrong indentation.\n"
           . "- Read the **console error message** carefully — it usually includes the line number.\n"
           . ($code ? "- Your current code is $line_count lines. I'm ready to analyze it!" : "- Write some code first and I'll help you find the issue!");

} elseif (preg_match('/\b(explain|what does|how does|describe|breakdown|understand)\b/', $user_msg)) {
    $reply = "📖 **Code Explanation:**\n\n"
           . "Click the **💡 Explain Code** button in the AI Actions panel for a full breakdown of your $lang_upper code — it covers:\n"
           . "- Logic flow & structure\n"
           . "- Loop & conditional analysis\n"
           . "- Performance complexity (Big-O)\n"
           . ($code_info ? "\n$code_info" : '');

} elseif (preg_match('/\b(optim|faster|speed|performance|efficient|improve|refactor)\b/', $user_msg)) {
    $reply = "⚡ **Optimization Guide for $lang_upper:**\n\n"
           . "Click **🚀 Optimize Code** for instant suggestions. General tips:\n"
           . "- Avoid unnecessary nested loops — aim for O(N) over O(N²).\n"
           . "- Use built-in functions (they're compiled and faster).\n"
           . ($language === 'python' ? "- Prefer list comprehensions over `for` + `.append()`.\n- Use `enumerate()` instead of `range(len())`.\n" : '')
           . ($language === 'javascript' ? "- Use `const`/`let` instead of `var`.\n- Use `Array.map()`, `filter()`, `reduce()` for collections.\n" : '')
           . ($language === 'java' ? "- Use `StringBuilder` instead of string concatenation in loops.\n- Prefer `ArrayList` over raw arrays for dynamic sizing.\n" : '');

} elseif (preg_match('/\b(review|quality|security|safe|vulnerabilit|rate|score)\b/', $user_msg)) {
    $reply = "🔐 **Code Review:**\n\n"
           . "Click **📊 Code Review** in the AI Actions panel for a full quality & security audit, including:\n"
           . "- Maintainability Index score\n"
           . "- Security vulnerability scan (`eval`, `exec`, `system` detection)\n"
           . "- Cyclomatic complexity rating\n"
           . "- Detailed reviewer feedback";

} elseif (preg_match('/\b(save|snippet|store|library|project)\b/', $user_msg)) {
    $reply = "💾 **Saving Code:**\n\n"
           . "Click the **Save** button in the toolbar to store your current code to your snippet library.\n"
           . "You can browse and reload saved snippets from the **Dashboard** → *Recent Projects & Snippets* section.";

} elseif (preg_match('/\b(share|link|url|collaborate|team|collab)\b/', $user_msg)) {
    $reply = "🔗 **Sharing & Collaboration:**\n\n"
           . "Click **Share** in the toolbar to generate a unique public URL for your current code.\n"
           . "Anyone with the link can view and edit the same workspace — great for team code reviews!\n"
           . "Use the **Live Comments** section to add team discussion notes under the shared project.";

} elseif (preg_match('/\b(language|switch|change|python|javascript|java|c\+\+|cpp|php|ruby|go|bash)\b/', $user_msg)) {
    $reply = "🌐 **Supported Languages:**\n\n"
           . "Nexus Studio supports: **Python, JavaScript (Node.js), PHP, C, C++, Java, Ruby, Go, Bash**\n\n"
           . "Use the **language selector** in the toolbar to switch. The editor will load the matching template automatically.\n"
           . "You're currently working in: **$lang_upper**";

} elseif (preg_match('/\b(theme|dark|light|font|settings|editor|appearance|color)\b/', $user_msg)) {
    $reply = "🎨 **Editor Settings:**\n\n"
           . "Go to **IDE Settings** (⚙️ in the sidebar) to customize:\n"
           . "- **Theme**: VS Code Dark, High Contrast, VS Code Light\n"
           . "- **Font Size**: 12px – 22px\n"
           . "- **Line Wrapping**, **Minimap**, **Auto-save** toggles\n"
           . "- **Username** for collaboration";

} elseif (preg_match('/\b(run|execute|compile|output|result)\b/', $user_msg)) {
    $reply = "▶️ **Running Code:**\n\n"
           . "Click the green **▶ Run** button in the toolbar (or press Ctrl+Enter if mapped).\n"
           . "The output will appear in the **Console** panel below the editor.\n"
           . "For programs that need input (like `input()` in Python), expand the **📥 Program Input** section before running.";

} else {
    $replies = [
        "🤖 I'm your **Nexus AI Assistant**! $code_info\nTry asking: *\"How do I fix this bug?\"*, *\"Explain my code\"*, or *\"How do I optimize this?\"*",
        "💡 $code_info\nYou can use the **AI Actions panel** (right side) for instant one-click analysis — Explain, Optimize, Fix Bugs, or get a full Code Review.",
        "🧠 Great question! For detailed $lang_upper analysis, click any **AI Action** button on the right. I can also help if you describe your specific problem!",
    ];
    $reply = $replies[array_rand($replies)];
}

echo json_encode([
    'success'   => true,
    'reply'     => $reply,
    'timestamp' => date('h:i A'),
]);
