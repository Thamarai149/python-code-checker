<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

$input = file_get_contents('php://input');
$data  = json_decode($input, true);

if (!$data || !isset($data['code']) || trim($data['code']) === '') {
    echo json_encode(['success' => false, 'error' => 'Editor is empty. Write some code first!']);
    exit;
}

$action   = isset($data['action'])   ? trim($data['action'])             : 'explain';
$code     = $data['code'];
$language = isset($data['language']) ? strtolower(trim($data['language'])): 'python';
$lang_upper = strtoupper($language);

$lines      = explode("\n", $code);
$line_count = count($lines);
$char_count = strlen($code);

$result_text = '';

// =====================================================================
// ACTION: EXPLAIN
// =====================================================================
if ($action === 'explain') {

    $fn_count = match ($language) {
        'python'     => substr_count($code, 'def '),
        'javascript',
        'typescript' => substr_count($code, 'function') + preg_match_all('/=>\s*{/', $code, $m),
        'php'        => substr_count($code, 'function '),
        'java',
        'kotlin'     => preg_match_all('/\bvoid\b|\bint\b|\bString\b|\bboolean\b.*\(/', $code, $m),
        'c', 'cpp'   => preg_match_all('/\w+\s+\w+\s*\([^;]*\)\s*{/', $code, $m),
        'ruby'       => substr_count($code, 'def '),
        'go'         => substr_count($code, 'func '),
        default      => 0,
    };

    $has_loop = preg_match('/\b(for|while|foreach|loop)\b/', $code);
    $has_cond = preg_match('/\b(if|else|elif|switch|case|ternary)\b/', $code);
    $has_class = preg_match('/\b(class|struct|interface|enum)\b/', $code);
    $has_import = preg_match('/\b(import|include|require|use)\b/', $code);

    $result_text  = "### 📖 Code Explanation — $lang_upper\n\n";
    $result_text .= "**Overview:** This {$lang_upper} file has **{$line_count} lines**, {$char_count} characters";
    if ($fn_count > 0) $result_text .= ", and defines **{$fn_count} function(s)**";
    $result_text .= ".\n\n";

    $result_text .= "**Structural Analysis:**\n";
    if ($has_import) $result_text .= "- 📦 **Dependencies:** External modules/libraries are imported at the top.\n";
    if ($has_class)  $result_text .= "- 🏛️ **OOP Design:** Defines classes or structured data types.\n";

    $result_text .= match ($language) {
        'python'                => "- 🐍 Executes top-to-bottom. Entry point runs at module level (or under `if __name__ == '__main__'`).\n",
        'javascript', 'typescript' => "- 🌐 Runs in a JS engine. Check for async/await or Promise chains if applicable.\n",
        'java', 'kotlin'        => "- ☕ Compiled JVM language. Execution starts at `main(String[] args)` method.\n",
        'c', 'cpp'              => "- ⚙️ Compiled native code. Entry point is the `main()` function.\n",
        'php'                   => "- 🐘 Interpreted server-side. Outputs directly to HTTP response.\n",
        'go'                    => "- 🔵 Statically typed. Entry via `func main()` in `package main`.\n",
        default                 => "- Direct script execution flow.\n",
    };

    $result_text .= "\n**Logic Breakdown:**\n";
    if ($has_loop) $result_text .= "- 🔁 **Iteration:** Contains `for` / `while` loops for repetitive operations.\n";
    if ($has_cond) $result_text .= "- 🔀 **Branching:** Uses `if`/`else` conditions to control execution paths.\n";
    if (!$has_loop && !$has_cond) $result_text .= "- ➡️ **Linear:** Instructions run sequentially without branching or loops.\n";

    $complexity = $line_count < 15 ? 'O(1)' : ($line_count < 60 ? 'O(N)' : 'O(N²) or higher');
    $result_text .= "\n**Performance Profile:**\n";
    $result_text .= "- ⏱️ Estimated Time Complexity: `$complexity`\n";
    $result_text .= "- 💾 Space Complexity: Depends on data structures used (likely O(N) or less).\n";

// =====================================================================
// ACTION: OPTIMIZE
// =====================================================================
} elseif ($action === 'optimize') {

    $result_text  = "### ⚡ Optimization Suggestions — $lang_upper\n\n";

    $tips = match ($language) {
        'python' => [
            "Use **list comprehensions** instead of `for` loops with `.append()` — they run faster in C-level Python.",
            "Cache repeated lookups: assign `obj.attr` to a local variable inside hot loops.",
            "Replace `range(len(arr))` with `enumerate(arr)` for cleaner, faster index+value iteration.",
            "Use `collections.defaultdict` or `Counter` instead of manual dictionary updates.",
            "Prefer `''.join(list)` over string concatenation (`+=`) in loops for O(N) instead of O(N²).",
        ],
        'javascript', 'typescript' => [
            "Use `const` and `let` (never `var`) to enable better JavaScript engine optimizations.",
            "Use `Array.map()`, `filter()`, and `reduce()` over raw `for` loops for clarity and performance.",
            "Avoid `document.getElementById()` inside loops — cache DOM references outside.",
            "Use `async/await` with `Promise.all()` for parallel async operations.",
            "Use `Set` or `Map` instead of plain objects for O(1) lookups on large datasets.",
        ],
        'java', 'kotlin' => [
            "Use `StringBuilder` instead of `String +` concatenation inside loops.",
            "Prefer `ArrayList` over arrays for dynamic sizing; use `LinkedList` for frequent insertions.",
            "Use streams (`stream().filter().map()`) for cleaner collection processing.",
            "Mark utility methods `static` to avoid object instantiation overhead.",
            "Use `HashMap` with initial capacity hints when key count is known.",
        ],
        'c', 'cpp' => [
            "Declare loop variables and counters outside loops to minimize stack allocs.",
            "Use `register` keyword hints for frequently accessed loop variables (where applicable).",
            "Prefer `++i` over `i++` in loops — avoids creating a temporary copy.",
            "Use `const` for read-only parameters to enable compiler optimizations.",
            "For C++: use `reserve()` on `std::vector` before pushing many elements.",
        ],
        'php' => [
            "Cache `count(\$array)` before loops — `count()` inside `for` recalculates every iteration.",
            "Use `isset()` instead of `array_key_exists()` for faster key checks.",
            "Prefer `echo` over `print` — `echo` is marginally faster.",
            "Use prepared statements (PDO) for any database queries.",
            "Enable OPcache in `php.ini` for significant bytecode caching speedup.",
        ],
        default => [
            "Reduce nested loops where possible — aim for O(N log N) or better.",
            "Cache repeated calculations or API calls where feasible.",
            "Use built-in language functions instead of manual implementations.",
            "Keep functions focused — single responsibility improves both readability and performance.",
        ],
    };

    $result_text .= "**Top Recommendations:**\n";
    foreach ($tips as $i => $tip) {
        $result_text .= ($i + 1) . ". $tip\n";
    }

    $result_text .= "\n**Quick Wins for this file:**\n";
    if (preg_match('/\bfor\b.*\brange\(len\(/', $code)) {
        $result_text .= "- ✅ Replace `range(len(...))` → use `enumerate()`\n";
    }
    if (preg_match('/\bprint\s*\(/', $code) && $language === 'python') {
        $result_text .= "- ✅ Batch print statements with `sys.stdout.write()` for large outputs.\n";
    }
    if ($line_count > 60) {
        $result_text .= "- ✅ Consider splitting this file into smaller modules for maintainability.\n";
    }

// =====================================================================
// ACTION: FIX (Bug Detection)
// =====================================================================
} elseif ($action === 'fix') {

    $result_text  = "### 🔧 Bug Fix Scan — $lang_upper\n\n";
    $issues = [];

    if ($language === 'python') {
        foreach ($lines as $idx => $line) {
            $s = trim($line);
            if ($s === '' || str_starts_with($s, '#')) continue;

            // Missing colon after block statements
            if (
                preg_match('/^(def |if |elif |else|for |while |with |class |try:|except|finally)/', $s) &&
                !str_ends_with($s, ':') &&
                !str_ends_with($s, '\\') &&
                !str_ends_with($s, ',')
            ) {
                $issues[] = "⚠️ Line " . ($idx + 1) . ": Missing `:` at end — `$s`";
            }

            // Common Python mistakes
            if (preg_match('/^\s*print\s+[^\(]/', $line)) {
                $issues[] = "⚠️ Line " . ($idx + 1) . ": `print` used without parentheses (Python 3 requires `print(...)`)";
            }
        }
    } elseif (in_array($language, ['c', 'cpp', 'java', 'javascript', 'typescript', 'php'])) {
        foreach ($lines as $idx => $line) {
            $s = trim($line);
            if ($s === '' || str_starts_with($s, '//') || str_starts_with($s, '#') || str_starts_with($s, '*')) continue;

            // Lines that should end with semicolon
            if (
                !str_ends_with($s, ';') &&
                !str_ends_with($s, '{') &&
                !str_ends_with($s, '}') &&
                !str_ends_with($s, '(') &&
                !str_ends_with($s, ',') &&
                !str_ends_with($s, '\\') &&
                !preg_match('/^(if|else|for|while|do|switch|try|catch|finally|class|function|#|\/\/)/', $s) &&
                strlen($s) > 3
            ) {
                $issues[] = "⚠️ Line " . ($idx + 1) . ": Verify semicolon — `$s`";
            }
        }
    }

    // Count open/close braces
    $open  = substr_count($code, '{');
    $close = substr_count($code, '}');
    if ($open !== $close) {
        $issues[] = "🔴 Brace mismatch: found **{$open} opening `{`** but **{$close} closing `}`** — check your block structure!";
    }

    $open_paren  = substr_count($code, '(');
    $close_paren = substr_count($code, ')');
    if ($open_paren !== $close_paren) {
        $issues[] = "🔴 Parenthesis mismatch: **{$open_paren} `(`** vs **{$close_paren} `)`** — a function call or condition may be unclosed!";
    }

    if (!empty($issues)) {
        $result_text .= "**⚠️ " . count($issues) . " potential issue(s) detected:**\n\n";
        foreach ($issues as $issue) {
            $result_text .= "- $issue\n";
        }
        $result_text .= "\n> **Tip:** Fix these line-by-line from top to bottom. One syntax error often causes cascading false-positive errors below it.";
    } else {
        $result_text .= "✅ **No critical syntax issues detected!**\n\n";
        $result_text .= "Your $lang_upper code looks syntactically clean. If you're still seeing runtime errors:\n";
        $result_text .= "- Check variable names and types\n";
        $result_text .= "- Validate input values and edge cases\n";
        $result_text .= "- Review any array index bounds or null references\n";
        $result_text .= "- Check function return values";
    }

// =====================================================================
// ACTION: REVIEW (Code Quality)
// =====================================================================
} elseif ($action === 'review') {

    $result_text = "### 📊 Code Quality & Security Review — $lang_upper\n\n";

    // Security scan
    $dangerous = ['eval(', 'exec(', 'system(', 'shell_exec(', 'passthru(', '__import__("os")', 'subprocess.call', 'Runtime.exec'];
    $sec_hits = [];
    foreach ($dangerous as $pattern) {
        if (str_contains($code, $pattern)) {
            $sec_hits[] = "`$pattern`";
        }
    }

    // Scores
    $maintain_score = 100;
    $maintain_score -= min(25, intval($line_count / 10));     // penalize very long files
    $fn_count_review = substr_count($code, 'def ') + substr_count($code, 'function') + substr_count($code, 'func ');
    if ($fn_count_review === 0 && $line_count > 15) $maintain_score -= 10; // no functions = poor modularity
    $has_comments = preg_match('/(#|\/\/|\/\*|"""|\'\'\')/', $code);
    if (!$has_comments) $maintain_score -= 10; // no documentation
    $maintain_score = max(0, min(100, $maintain_score));

    $complexity_label = $line_count < 20 ? '🟢 Low' : ($line_count < 60 ? '🟡 Medium' : '🔴 High');
    $sec_status = empty($sec_hits)
        ? '🟢 **SECURE** — No dangerous calls detected'
        : '🔴 **VULNERABLE** — Found: ' . implode(', ', $sec_hits);

    $result_text .= "**📈 Quality Metrics:**\n";
    $result_text .= "| Metric | Value |\n";
    $result_text .= "|--------|-------|\n";
    $result_text .= "| Maintainability Index | **{$maintain_score}/100** |\n";
    $result_text .= "| Lines of Code | **{$line_count}** |\n";
    $result_text .= "| Cyclomatic Complexity | **{$complexity_label}** |\n";
    $result_text .= "| Security Status | {$sec_status} |\n";
    $result_text .= "| Has Comments/Docs | " . ($has_comments ? '✅ Yes' : '❌ No') . " |\n\n";

    $result_text .= "**📝 Reviewer Feedback:**\n";
    $result_text .= "1. **Modularity:** " . ($fn_count_review > 0 ? "Good — code is organized into functions." : "Consider breaking logic into reusable functions.") . "\n";
    $result_text .= "2. **Documentation:** " . ($has_comments ? "Comments found — keep them updated as code changes." : "Add comments/docstrings to explain complex logic.") . "\n";
    $result_text .= "3. **Security:** " . (empty($sec_hits) ? "No dangerous patterns found. Run inside sandbox for untrusted inputs." : "⚠️ Found potentially unsafe calls — review and replace with safer alternatives.") . "\n";
    $result_text .= "4. **Complexity:** " . ($line_count > 60 ? "File is large. Consider splitting into modules." : "File length is manageable.") . "\n";
    $result_text .= "5. **Best Practices:** Follow $lang_upper naming conventions and keep functions under 30 lines where possible.";

} else {
    $result_text = "❌ Unknown action `$action`. Valid options: `explain`, `optimize`, `fix`, `review`.";
}

echo json_encode(['success' => true, 'result' => $result_text]);
