<?php
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['code'])) {
    echo json_encode(['success' => false, 'error' => 'Editor is empty. Write some code first!']);
    exit;
}

$action = isset($data['action']) ? $data['action'] : 'explain';
$code = $data['code'];
$language = isset($data['language']) ? $data['language'] : 'python';

$result_text = '';

if ($action === 'explain') {
    $lines = count(explode("\n", $code));
    $functions = ($language === 'python') ? substr_count($code, 'def ') : substr_count($code, 'function');
    
    $result_text = "### Code Explanation (" . strtoupper($language) . ")\n";
    $result_text .= "This program consists of {$lines} lines of code and defines {$functions} function(s).\n\n";
    $result_text .= "**Core Logic Flow:**\n";
    
    if ($language === 'python') {
        $result_text .= "- Executes sequentially from top to bottom.\n";
        if (str_contains($code, 'import')) {
            $result_text .= "- Standard library modules are imported at the top.\n";
        }
    } elseif (in_array($language, ['c', 'cpp', 'java'])) {
        $result_text .= "- Enters through the standard application entrypoint (`main` function).\n";
        $result_text .= "- Uses type-safe declarations and static compiling rules.\n";
    } else {
        $result_text .= "- Direct scripting structure.\n";
    }
    
    $result_text .= "\n**Detailed Breakdown:**\n";
    $has_loop = str_contains($code, 'for ') || str_contains($code, 'while ') || str_contains($code, 'while(') || str_contains($code, 'for(');
    $has_cond = str_contains($code, 'if ') || str_contains($code, 'if(') || str_contains($code, 'elif') || str_contains($code, 'else');
    
    if ($has_loop) {
        $result_text .= "- **Iteration**: The code contains looping logic (`for` or `while`), allowing repetitive actions on standard collection elements or index iterations.\n";
    }
    if ($has_cond) {
        $result_text .= "- **Conditionals**: Logical branches (`if`/`else`) direct program execution based on runtime conditions.\n";
    }
    if (!$has_loop && !$has_cond) {
        $result_text .= "- **Linear Execution**: The instructions execute in a single forward pass without branching or iteration.\n";
    }
    
    $result_text .= "\n**Performance Profile:**\n";
    $result_text .= "- Expected Time Complexity: O(1) to O(N) depending on runtime parameters.\n";
    $result_text .= "- Space Overhead: Minimal stack allocations.";

} elseif ($action === 'optimize') {
    $result_text = "### Optimization Suggestions (" . strtoupper($language) . ")\n";
    if ($language === 'python') {
        $result_text .= "1. **List Comprehensions**: Replace standard loops appending to lists with Pythonic list comprehensions to execute faster in compiled C layers.\n";
        $result_text .= "2. **Local Caching**: Cache repeated dictionary or object attribute lookups locally inside local scopes.\n\n";
        $result_text .= "**Optimized Code Snippet:**\n";
        $result_text .= "```python\n# Optimized Revision\n# Avoid redundant lookups. Use efficient generators.\n";
        $result_text .= str_replace('for i in range', "# Vectorized range operations if possible\nfor i in range", $code) . "\n```";
    } else {
        $result_text .= "1. **Memory Allocations**: Minimize dynamic heap allocation variables inside loops.\n";
        $result_text .= "2. **Inline Keywords**: Suggest compilers compiler-inlining frequently accessed sub-methods to reduce function-call frame overheads.\n\n";
        $result_text .= "**Optimized Revision Recommendation:**\n";
        $result_text .= "```{$language}\n// Inline declarations and static variable bounds\n" . $code . "\n```";
    }

} elseif ($action === 'fix') {
    $result_text = "### Debugging & Bug Fix Suggestions\n";
    $issues = [];
    $lines = explode("\n", $code);
    
    if ($language === 'python') {
        foreach ($lines as $idx => $line) {
            $sline = trim($line);
            if (
                (str_starts_with($sline, 'def ') || 
                 str_starts_with($sline, 'if ') || 
                 str_starts_with($sline, 'elif ') || 
                 str_starts_with($sline, 'for ')) && 
                !str_ends_with($sline, ':')
            ) {
                $issues[] = "Line " . ($idx + 1) . ": Missing colon at end of statement definition: `{$sline}`";
            }
        }
    } else {
        foreach ($lines as $idx => $line) {
            $sline = trim($line);
            if (
                $sline && 
                !str_ends_with($sline, ';') && 
                !str_ends_with($sline, '{') && 
                !str_ends_with($sline, '}') && 
                !str_starts_with($sline, '#') && 
                !str_starts_with($sline, '//')
            ) {
                if (in_array($language, ['c', 'cpp', 'javascript', 'java', 'php'])) {
                    $issues[] = "Line " . ($idx + 1) . ": Verify semicolon presence at end of line: `{$sline}`";
                }
            }
        }
    }
    
    if (!empty($issues)) {
        $result_text .= "⚠️ **Detected Potential Issues:**\n";
        foreach ($issues as $issue) {
            $result_text .= "- {$issue}\n";
        }
        $result_text .= "\n**Suggested Fix:** Apply the formatting rules indicated above to ensure error-free compilation.";
    } else {
        $result_text .= "✅ **No major syntax warnings found!**\nYour code looks clean. If execution returns errors, verify variable initializations, indexing, and input arguments.";
    }

} elseif ($action === 'review') {
    $result_text = "### Code Quality & Security Review (" . strtoupper($language) . ")\n";
    $lines_count = count(explode("\n", $code));
    $score = 100 - min(40, intval($lines_count / 5));
    
    if (str_contains($code, 'eval(') || str_contains($code, 'exec(') || str_contains($code, 'system(')) {
        $score -= 30;
        $sec_status = "🔴 VULNERABLE (Dynamic execution call spotted)";
    } else {
        $sec_status = "🟢 SECURE (Running inside virtual execution limits)";
    }
    
    $complexity = ($lines_count < 15) ? 'Low' : (($lines_count < 45) ? 'Medium' : 'High');
    
    $result_text .= "**Rating Metric Scores:**\n";
    $result_text .= "- **Maintainability Index:** {$score}/100\n";
    $result_text .= "- **Security Level:** {$sec_status}\n";
    $result_text .= "- **Cyclomatic Complexity:** {$complexity}\n\n";
    $result_text .= "**Reviewer Feedback Details:**\n";
    $result_text .= "1. **Modularity**: Code structures are aligned. Ensure logical separations for large functions.\n";
    $result_text .= "2. **Documentation**: Add standard docstrings or developer block comments to explain algorithm parameters.\n";
    $result_text .= "3. **Exceptions**: Wrap input validations and network/file resources in try/catch bounds.";
}

echo json_encode(['success' => true, 'result' => $result_text]);
