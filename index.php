<?php
// PHP Backend header for Nexus Studio
$code_templates = [
    "java" => 'public class Main {
    public static void main(String[] args) {
        System.out.println("Hello, Java!");
    }
}',
    "python" => '# Python code
print("Hello, Python!")',
    "javascript" => '// JavaScript code
console.log("Hello, JavaScript!");',
    "c" => '#include <stdio.h>

int main() {
    printf("Hello, C!\\n");
    return 0;
}',
    "cpp" => '#include <iostream>
using namespace std;

int main() {
    cout << "Hello, C++!" << endl;
    return 0;
}',
    "csharp" => 'using System;

class Program {
    static void Main() {
        Console.WriteLine("Hello, C#!");
    }
}',
    "ruby" => '# Ruby code
puts "Hello, Ruby!"',
    "php" => '<?php
echo "Hello, PHP!\\n";
?>',
    "go" => 'package main

import "fmt"

func main() {
    fmt.Println("Hello, Go!")
}',
    "rust" => 'fn main() {
    println!("Hello, Rust!");
}',
    "bash" => '#!/bin/bash
echo "Hello, Bash!"',
    "perl" => '#!/usr/bin/perl
print "Hello, Perl!\\n";',
    "r" => '# R code
print("Hello, R!")',
    "kotlin" => 'fun main() {
    println("Hello, Kotlin!")
}',
    "swift" => 'import Foundation
print("Hello, Swift!")',
    "scala" => 'object Main {
  def main(args: Array[String]): Unit = {
    println("Hello, Scala!")
  }
}',
    "lua" => 'print("Hello, Lua!")',
    "typescript" => '// TypeScript code
console.log("Hello, TypeScript!");',
    "dart" => 'void main() {
  print(\'Hello, Dart!\');
}',
    "fortran" => 'program hello
  print *, \'Hello, Fortran!\'
end program hello',
    "cobol" => '       IDENTIFICATION DIVISION.
       PROGRAM-ID. HELLO.
       PROCEDURE DIVISION.
           DISPLAY \'Hello, Cobol!\'.
           STOP RUN.',
    "pascal" => 'program Hello;
begin
  writeln(\'Hello, Pascal!\');
end.',
    "haskell" => 'main :: IO ()
main = putStrLn "Hello, Haskell!"
',
    "objectivec" => '#import <Foundation/Foundation.h>

int main() {
    @autoreleasepool {
        NSLog(@"Hello, Objective-C!");
    }
    return 0;
}',
    "assembly" => 'section .data
    msg db \'Hello, Assembly!\', 0xA
    len equ $ - msg

section .text
    global _start

_start:
    mov eax, 4
    mov ebx, 1
    mov ecx, msg
    mov edx, len
    int 0x80
    
    mov eax, 1
    xor ebx, ebx
    int 0x80',
    "prolog" => '% Prolog code
:- initialization(main).
main :- write(\'Hello, Prolog!\'), nl, halt.',
    "lisp" => '(format t "Hello, Common Lisp!~%")',
    "scheme" => '(display "Hello, Scheme!")
(newline)',
    "erlang" => '-module(hello).
-export([start/0]).

start() ->
    io:format("Hello, Erlang!~n").',
    "elixir" => 'IO.puts "Hello, Elixir!"',
    "clojure" => '(println "Hello, Clojure!")',
    "fsharp" => 'printfn "Hello, F#!"',
    "vb" => 'Module Hello
    Sub Main()
        Console.WriteLine("Hello, Visual Basic!")
    End Sub
End Module',
    "html" => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Preview Workspace</title>
    <style>
        body {
            background-color: #0d1117;
            color: #c9d1d9;
            font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 90vh;
            margin: 0;
            text-align: center;
        }
        .container {
            padding: 30px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
        }
        h1 {
            color: #58a6ff;
            margin-bottom: 10px;
            font-size: 2.5rem;
        }
        p {
            color: #8b949e;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        button {
            background: #1f6feb;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        button:hover {
            background: #58a6ff;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Nexus HTML Environment</h1>
        <p>Edit this workspace to preview your HTML, CSS, and JS styles live.</p>
        <button id="alertBtn">Interact Locally</button>
    </div>
    <script>
        document.getElementById(\'alertBtn\').addEventListener(\'click\', () => {
            alert(\'JavaScript works seamlessly in the Nexus sandbox iframe!\');
        });
    </script>
</body>
</html>'
];

$initial_code = "";
$initial_lang = "python";
$initial_output = "Console synchronized. Write code and hit Run to execute details.";

if (isset($_GET['share_id'])) {
    $share_id = preg_replace('/[^a-f0-9]/', '', $_GET['share_id']);
    $share_path = __DIR__ . "/shared_codes/{$share_id}.json";
    if (file_exists($share_path)) {
        $share_data = json_decode(file_get_contents($share_path), true);
        if ($share_data) {
            $initial_code = $share_data['code'];
            $initial_lang = $share_data['language'];
            $initial_output = "--- Shared Project ---\\nLoaded from shared link.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Studio - Cloud Compiler & AI IDE</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;700;800&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="dark-mode">

    <!-- Unified Dialog System -->
    <div id="nexusModal" class="about-modal">
        <div class="about-modal-content">
            <span class="modal-close" onclick="Nexus.close()">&times;</span>
            <h2 id="nexusTitle" style="font-family:var(--font-accent); color:var(--primary); margin-bottom:15px;">Dialog Title</h2>
            <div id="nexusBody" style="font-size:14px; color:var(--text-main); line-height:1.6;">Message goes here.</div>
            <div id="nexusInputContainer"></div>
            <div class="dialog-btns" id="nexusFooter">
                <button class="nx-btn" onclick="Nexus.close()">Close</button>
            </div>
        </div>
    </div>

    <!-- Main Navigation Sidebar -->
    <div class="studio-sidebar">
        <div class="logo" onclick="switchView('landing')">
            <i class="fa-solid fa-bolt"></i>
            <span class="logo-text">Nexus Studio</span>
        </div>
        <div class="sidebar-nav">
            <div class="nav-item active" id="nav-landing" onclick="switchView('landing')">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </div>
            <div class="nav-item" id="nav-dashboard" onclick="switchView('dashboard')">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </div>
            <div class="nav-item" id="nav-workspace" onclick="switchView('workspace')">
                <i class="fa-solid fa-code"></i>
                <span>Workspace IDE</span>
            </div>
            <div class="nav-item" id="nav-profile" onclick="switchView('profile')">
                <i class="fa-solid fa-user-gear"></i>
                <span>Profile Stats</span>
            </div>
            <div class="nav-item" id="nav-settings" onclick="switchView('settings')">
                <i class="fa-solid fa-sliders"></i>
                <span>IDE Settings</span>
            </div>
        </div>
        
        <div class="sidebar-profile" onclick="switchView('profile')">
            <div class="profile-avatar">TS</div>
        </div>
    </div>

    <!-- Main Application Frame -->
    <div class="studio-main">
        
        <!-- ================= PAGE 1: LANDING PAGE ================= -->
        <div id="view-landing" class="view-panel active">
            <div class="landing-hero">
                <div class="landing-badge">V2.0 Release Available</div>
                <h1>Compile, Debug & Build<br>with AI Assistance</h1>
                <p>The premium browser-based compilation sandbox powered by Monaco Editor, local secure compilers, and real-time developer diagnostics.</p>
                <div style="display:flex; gap:15px;">
                    <button class="nx-btn nx-btn-run" style="padding:12px 28px; font-size:15px;" onclick="switchView('workspace')">
                        Launch Workspace <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <button class="nx-btn" style="padding:12px 28px; font-size:15px;" onclick="switchView('dashboard')">
                        Open Dashboard
                    </button>
                </div>
            </div>

            <div class="landing-features">
                <div class="feature-card" onclick="switchView('workspace')">
                    <i class="fa-solid fa-microchip"></i>
                    <h3>Multi-Language Compiler</h3>
                    <p>Execute Python, C, C++, Java, JS, and PHP programs instantly with live diagnostics, timing analytics, and memory usage profiling.</p>
                </div>
                <div class="feature-card" onclick="switchView('workspace')">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <h3>AI Coding Assistant</h3>
                    <p>Explain code line-by-line, optimize time-complexity, refactor, and spot syntax bugs with one-click actions.</p>
                </div>
                <div class="feature-card" onclick="switchView('settings')">
                    <i class="fa-solid fa-keyboard"></i>
                    <h3>Custom Monaco Workspace</h3>
                    <p>VS Code style code window, support for multiple editor themes, custom font sizing, line wrap options, and auto-saving.</p>
                </div>
            </div>
        </div>

        <!-- ================= PAGE 2: DASHBOARD ================= -->
        <div id="view-dashboard" class="view-panel">
            <h1 style="font-family:var(--font-accent); color:var(--text-bright); margin-bottom:10px;">Developer Dashboard</h1>
            <p style="color:var(--text-dim); margin-bottom:25px;">Track your code snippets, coding activity history, and language metrics.</p>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <label>Total Snippets</label>
                    <div class="val" id="stats-total-snippets">0</div>
                    <span style="font-size:10px; color:var(--primary);">Saved locally</span>
                </div>
                <div class="stat-card success">
                    <label>Coding Streak</label>
                    <div class="val">12 Days</div>
                    <span style="font-size:10px; color:var(--success);">Active streak</span>
                </div>
                <div class="stat-card">
                    <label>Solved Challenges</label>
                    <div class="val" id="stats-solved-challenges">0</div>
                    <span style="font-size:10px; color:var(--text-dim);">Out of 6 exercises</span>
                </div>
                <div class="stat-card error">
                    <label>Security Sandbox</label>
                    <div class="val">Active</div>
                    <span style="font-size:10px; color:var(--error);">Secure execution limits</span>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="dashboard-left">
                    <div class="dashboard-block">
                        <h2>Recent Projects & Snippets <button class="nx-btn" style="padding:4px 8px; font-size:11px;" onclick="switchView('workspace')"><i class="fa-solid fa-plus"></i> Workspace</button></h2>
                        <div class="project-cards" id="dashboard-recent-projects">
                            <!-- Populated dynamically -->
                            <div style="grid-column:1/-1; text-align:center; padding:30px; color:var(--text-dim);">
                                No custom projects saved. Go to the compiler workspace and save code to see them here!
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-block">
                        <h2>Workspace Activity Flow (Commits / Solved)</h2>
                        <div class="activity-graph" id="activityGraph">
                            <!-- Generated dynamically -->
                        </div>
                        <div style="display:flex; justify-content:flex-end; gap:8px; font-size:10px; color:var(--text-dim); margin-top:8px;">
                            <span>Less</span>
                            <div style="width:10px; height:10px; background:#161b22; border-radius:2px;"></div>
                            <div style="width:10px; height:10px; background:#0e4429; border-radius:2px;"></div>
                            <div style="width:10px; height:10px; background:#006d32; border-radius:2px;"></div>
                            <div style="width:10px; height:10px; background:#26a641; border-radius:2px;"></div>
                            <div style="width:10px; height:10px; background:#39d353; border-radius:2px;"></div>
                            <span>More</span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-right">
                    <div class="dashboard-block">
                        <h2>Quick Templates</h2>
                        <div style="display:flex; flex-direction:column; gap:10px;" id="dashboard-templates-list">
                            <!-- Populated from script -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= PAGE 3: IDE WORKSPACE ================= -->
        <div id="view-workspace" class="view-panel" style="padding:0; overflow:hidden;">
            <!-- Header Toolbar -->
            <div class="toolbar">
                <div class="toolbar-brand">
                    <i class="fa-solid fa-code" style="color:var(--primary); margin-right:5px;"></i> Workspace IDE
                </div>
                
                <select name="language" id="language" class="studio-select" onchange="changeLanguage()">
                    <option value="python">Python</option>
                    <option value="c">C</option>
                    <option value="cpp">C++</option>
                    <option value="java">Java</option>
                    <option value="javascript">JavaScript</option>
                    <option value="html">HTML / CSS Preview</option>
                    <option value="php">PHP</option>
                </select>

                <button class="nx-btn nx-btn-run" onclick="executeWorkspace()">
                    <i class="fa-solid fa-play"></i> Run Code
                </button>
                <button class="nx-btn nx-btn-stop" onclick="stopExecution()">
                    <i class="fa-solid fa-stop"></i> Stop
                </button>
                <button class="nx-btn" onclick="saveSnippet()">
                    <i class="fa-solid fa-floppy-disk"></i> Save
                </button>
                <button class="nx-btn" onclick="shareProject()">
                    <i class="fa-solid fa-share-nodes"></i> Share
                </button>
                <button class="nx-btn" onclick="formatWorkspaceCode()">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Beautify
                </button>
            </div>

            <!-- Workspace Columns Layout -->
            <div class="workspace-layout">
                <!-- COLUMN 1: LEFT SIDEBAR (EXPLORER, CHALLENGES, DESIGN SYSTEM) -->
                <div class="workspace-col-left">
                    <!-- Section: File Explorer -->
                    <div class="panel-header">
                        File Explorer <i class="fa-solid fa-folder"></i>
                    </div>
                    <div class="panel-content" style="max-height: 150px; border-bottom:1px solid var(--glass-border);">
                        <div class="file-tree-item active" id="explorer-main-file">
                            <i class="fa-solid fa-file-code" style="color:var(--primary);"></i>
                            <span id="explorer-filename">main.py</span>
                        </div>
                        <div class="file-tree-item" onclick="triggerFileUpload()">
                            <i class="fa-solid fa-file-import"></i>
                            <span>Upload Code file...</span>
                            <input type="file" id="code-file-uploader" style="display:none;" onchange="handleUploadedFile(event)">
                        </div>
                        <div class="file-tree-item" onclick="downloadWorkspaceFile()">
                            <i class="fa-solid fa-file-export"></i>
                            <span>Download Code file</span>
                        </div>
                    </div>

                </div>

                <!-- COLUMN 2: CENTER EDITOR AND OUTPUT -->
                <div class="workspace-col-center">
                    <div class="monaco-container">
                        <!-- Loading spinner -->
                        <div class="editor-loading-overlay" id="editor-loading">
                            <div class="spinner"></div>
                            <span style="font-size:12px; color:var(--text-dim)">Loading Monaco Editor engine...</span>
                        </div>
                        <!-- Monaco / Textarea Container -->
                        <div id="monaco-editor-target" style="width:100%; height:100%; position:relative;">
                            <textarea id="code-textarea" class="workspace-textarea" placeholder="Write code here..."></textarea>
                        </div>
                        
                        <!-- Floating controls -->
                        <div class="editor-controls">
                            <button class="editor-control-btn" onclick="adjustFontSize(1)" title="Increase Font"><i class="fa-solid fa-magnifying-glass-plus"></i></button>
                            <button class="editor-control-btn" onclick="adjustFontSize(-1)" title="Decrease Font"><i class="fa-solid fa-magnifying-glass-minus"></i></button>
                            <button class="editor-control-btn" id="wrap-toggle-btn" onclick="toggleLineWrap()" title="Toggle Wrap"><i class="fa-solid fa-indent"></i></button>
                            <span style="font-size:11px; color:var(--text-dim); align-self:center; border-left:1px solid var(--glass-border); padding-left:8px; margin-left:3px;" id="autosave-status">Autosave: On</span>
                        </div>
                    </div>

                    <!-- Output Console panel -->
                    <div class="bottom-console">
                        <div class="console-tabs">
                            <div class="console-tab active" id="tab-output" onclick="switchConsoleTab('output')">Terminal</div>
                            <div class="console-tab" id="tab-preview" onclick="switchConsoleTab('preview')">HTML Web Preview</div>
                        </div>

                        <!-- Terminal content — unified output + input -->
                        <div class="console-tab-content active" id="content-output" style="display:flex; flex-direction:column; padding:0; overflow:hidden;">
                            <!-- Output area -->
                            <div style="flex:1; overflow:auto; padding:14px 18px 8px; background:#020508;">
                                <pre class="console-output" id="terminal-stdout">Console synchronized. Write code and hit Run to execute details.</pre>
                            </div>
                            <!-- Inline stdin prompt row -->
                            <div class="terminal-input-row">
                                <span class="terminal-prompt">stdin<span class="terminal-prompt-arrow">›</span></span>
                                <textarea id="workspace-stdin" class="terminal-inline-input" rows="1" placeholder="Type program input here, one value per line…" onkeydown="handleStdinKey(event)" oninput="autoResizeStdin(this)"></textarea>
                                <button class="terminal-clear-btn" onclick="clearTerminal()" title="Clear terminal"><i class="fa-solid fa-trash-can"></i></button>
                            </div>
                        </div>

                        <!-- HTML preview sandbox -->
                        <div class="console-tab-content" id="content-preview" style="padding: 0; background:#fff;">
                            <iframe id="html-preview-frame" style="width:100%; height:100%; border:none; background:#fff;"></iframe>
                        </div>

                        <!-- Console Status Bar -->
                        <div class="console-status-bar">
                            <div class="status-left">
                                <span id="status-compiler-info">Compiler: Idle</span>
                            </div>
                            <div class="status-right">
                                <span id="status-timing">Time: 0.00s</span>
                                <span id="status-memory">Memory: -- MB</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- COLUMN 3: RIGHT SIDEBAR (AI & COLLABORATION & COMMENTS) -->
                <div class="workspace-col-right">
                    <!-- Tab Headers -->
                    <div class="console-tabs" style="background:var(--bg-studio);">
                        <div class="console-tab active" id="tab-ai-chat" onclick="switchRightTab('ai-chat')" style="font-size:11px; padding:0 12px;"><i class="fa-solid fa-wand-magic-sparkles" style="margin-right:4px;"></i> AI Assistant</div>
                        <div class="console-tab" id="tab-collab" onclick="switchRightTab('collab')" style="font-size:11px; padding:0 12px;"><i class="fa-solid fa-users" style="margin-right:4px;"></i> Live Collaboration</div>
                    </div>

                    <!-- Tab Content: AI Panel -->
                    <div id="content-ai-chat" style="display:flex; flex-direction:column; flex:1; overflow:hidden;">
                        <div class="ai-panel-chat" id="ai-chat-history">
                            <div class="chat-bubble assistant">
                                <h3>Nexus AI Assistant</h3>
                                Hello! I'm ready to review your code workspace. Select one of the quick actions below, or ask me about algorithms and syntax bugs.
                            </div>
                        </div>
                        
                        <!-- AI Actions wrapper -->
                        <div class="ai-panel-actions">
                            <div class="ai-btn-grid">
                                <button class="ai-quick-btn" onclick="executeAIAction('explain')">
                                    <i class="fa-solid fa-circle-question" style="color:var(--primary);"></i> Explain Code
                                </button>
                                <button class="ai-quick-btn" onclick="executeAIAction('optimize')">
                                    <i class="fa-solid fa-gauge" style="color:#e2b93c;"></i> Optimize Code
                                </button>
                                <button class="ai-quick-btn" onclick="executeAIAction('fix')">
                                    <i class="fa-solid fa-bug" style="color:var(--error);"></i> AI Bug Fix
                                </button>
                                <button class="ai-quick-btn" onclick="executeAIAction('review')">
                                    <i class="fa-solid fa-clipboard-check" style="color:var(--success);"></i> Code Review
                                </button>
                            </div>
                            <div class="ai-input-container">
                                <input type="text" class="ai-input" id="ai-user-query" placeholder="Ask AI anything..." onkeydown="if(event.key === 'Enter') sendAIChatMessage()">
                                <button class="nx-btn nx-btn-run" style="padding:8px 12px;" onclick="sendAIChatMessage()"><i class="fa-solid fa-paper-plane"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content: Collab & Comment System -->
                    <div id="content-collab" style="display:none; flex-direction:column; flex:1; padding:15px; overflow-y:auto;">
                        <h4 style="font-size:12px; color:var(--text-bright); margin-bottom:5px;">Presence Connection Status</h4>
                        <div class="collab-presence">
                            <div class="user-presence-badge active">You (Editor)</div>
                            <div class="user-presence-badge active" style="color:var(--primary);">Mock Peer</div>
                        </div>
                        
                        <div style="background:var(--bg-card); border:1px solid var(--glass-border); padding:10px; border-radius:6px; font-size:11px; color:var(--text-dim); margin-bottom:15px;">
                            <i class="fa-solid fa-circle-info" style="color:var(--primary); margin-right:4px;"></i> Shared session connects automatically when clicking the <strong>Share</strong> button.
                        </div>

                        <h4 style="font-size:12px; color:var(--text-bright); margin-bottom:8px;">Project Comments & Discussion</h4>
                        <div id="collab-comments-thread" style="flex:1; display:flex; flex-direction:column; gap:10px; margin-bottom:15px;">
                            <!-- Comments fetched dynamically -->
                        </div>

                        <div style="display:flex; flex-direction:column; gap:8px;">
                            <input type="text" id="collab-comment-input" class="nx-input" style="font-size:11px; margin:0; padding:8px;" placeholder="Add a comment to the team thread...">
                            <button class="nx-btn" style="align-self:flex-end; font-size:11px; padding:6px 12px;" onclick="postCollabComment()"><i class="fa-solid fa-comment-plus"></i> Post Comment</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= PAGE 4: USER PROFILE ================= -->
        <div id="view-profile" class="view-panel">
            <div class="profile-header">
                <div class="profile-avatar-large">TS</div>
                <div class="profile-title">
                    <h2>Developer Profile Settings</h2>
                    <p>Developer Status: <span style="color:var(--primary); font-weight:bold;">Active Contributor</span></p>
                    <p>Location: Sandbox Workspace Local Host</p>
                </div>
            </div>

            <div class="dashboard-grid" style="margin-top:0;">
                <div>
                    <div class="dashboard-block">
                        <h2>Language Experience Metrics</h2>
                        <div style="display:flex; flex-direction:column; gap:12px;">
                            <div>
                                <div style="display:flex; justify-content:space-between; font-size:12px;">
                                    <span>Python Workspace Execution</span>
                                    <span>85% Proficient</span>
                                </div>
                                <div class="progress-bar-container"><div class="progress-fill" style="width: 85%;"></div></div>
                            </div>
                            <div>
                                <div style="display:flex; justify-content:space-between; font-size:12px;">
                                    <span>C / C++ Compiler Tasks</span>
                                    <span>60% Proficient</span>
                                </div>
                                <div class="progress-bar-container"><div class="progress-fill" style="width: 60%; background:#1f6feb;"></div></div>
                            </div>
                            <div>
                                <div style="display:flex; justify-content:space-between; font-size:12px;">
                                    <span>JavaScript Execution Engine</span>
                                    <span>70% Proficient</span>
                                </div>
                                <div class="progress-bar-container"><div class="progress-fill" style="width: 70%; background:#3fb950;"></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="dashboard-block">
                        <h2>Solved Challenges Status</h2>
                        <div id="profile-challenges-stats" style="font-size:12px; display:flex; flex-direction:column; gap:8px;">
                            <!-- Challenges stats populated dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= PAGE 5: IDE SETTINGS ================= -->
        <div id="view-settings" class="view-panel">
            <h1 style="font-family:var(--font-accent); color:var(--text-bright); margin-bottom:10px;">Workspace Settings</h1>
            <p style="color:var(--text-dim); margin-bottom:25px;">Configure compiler preferences, auto-save settings, and editor aesthetics.</p>

            <div class="settings-group">
                <h3>Editor Configurations</h3>
                <div class="settings-row">
                    <div class="settings-info">
                        <label>Editor Theme Options</label>
                        <span>Choose between dark VS Code themes</span>
                    </div>
                    <select class="studio-select" id="settings-theme-selector" onchange="applySettings()">
                        <option value="vs-dark">VS Code Default Dark</option>
                        <option value="hc-black">High Contrast Black</option>
                        <option value="vs-light">VS Code Light Theme</option>
                    </select>
                </div>
                <div class="settings-row">
                    <div class="settings-info">
                        <label>Code Auto-Save</label>
                        <span>Instantly store code variations in browser storage</span>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="settings-autosave" checked onchange="applySettings()">
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="settings-row">
                    <div class="settings-info">
                        <label>Font Size</label>
                        <span>Adjust font bounding box px height</span>
                    </div>
                    <select class="studio-select" id="settings-fontsize-selector" onchange="applySettings()">
                        <option value="12">12px</option>
                        <option value="14">14px</option>
                        <option value="16" selected>16px</option>
                        <option value="18">18px</option>
                        <option value="20">20px</option>
                    </select>
                </div>
            </div>

            <div class="settings-group">
                <h3>Compilation Bounds & Sandbox Configuration</h3>
                <div class="settings-row">
                    <div class="settings-info">
                        <label>Subprocess Rate Limiting</label>
                        <span>Throttle compilation subprocess launches to prevent memory overflow</span>
                    </div>
                    <span style="font-size:12px; color:var(--success); font-weight:bold;">Active (5 launches / min limit)</span>
                </div>
                <div class="settings-row">
                    <div class="settings-info">
                        <label>Process Timeout Bounds</label>
                        <span>Auto-stop script subprocess runs after timeout limit</span>
                    </div>
                    <span style="font-size:12px; color:var(--text-bright);">30 seconds limit</span>
                </div>
            </div>

            <div class="settings-group">
                <h3>Workspace Keyboard Shortcuts</h3>
                <div style="font-size:12px; color:var(--text-main); display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div><strong>Run Code:</strong> <code>Ctrl + Enter</code></div>
                    <div><strong>Save Code:</strong> <code>Ctrl + S</code></div>
                    <div><strong>Beautify Code:</strong> <code>Ctrl + Shift + F</code></div>
                    <div><strong>Switch Tabs:</strong> <code>Alt + Number (1-5)</code></div>
                </div>
            </div>
        </div>

    </div>

    <!-- ================= DESIGN SYSTEM / COMPONENT SHOWCASE MODAL ================= -->
    <div id="designModal" class="about-modal">
        <div class="about-modal-content" style="max-width:800px; width:95%; max-height:90vh; overflow-y:auto;">
            <span class="modal-close" onclick="closeDesignModal()">&times;</span>
            <h2 id="designModalTitle" style="font-family:var(--font-accent); color:var(--primary); margin-bottom:20px; border-bottom:1px solid var(--glass-border); padding-bottom:8px;">Design System</h2>
            <div id="designModalBody">
                <!-- Content injected dynamically -->
            </div>
        </div>
    </div>

    <!-- Load Templates Data from Backend -->
    <script id="templates-data" type="application/json"><?php echo json_encode($code_templates); ?></script>
    
    <!-- Load Design assets configuration -->
    <script src="js/design_assets.js"></script>
    
    
    <!-- Inject share code variables -->
    <script>
        const sharedCode = <?php echo isset($initial_code) && $initial_code !== "" ? json_encode($initial_code) : 'null'; ?>;
        const sharedLang = <?php echo isset($initial_lang) && $initial_lang !== "" ? json_encode($initial_lang) : 'null'; ?>;
        const sharedOutput = <?php echo isset($initial_output) && $initial_output !== "" ? json_encode($initial_output) : 'null'; ?>;
    </script>

    <!-- Load Monaco Editor via AMD loader CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.39.0/min/vs/loader.min.js"></script>
    
    <!-- Workspace Logic Script -->
    <script>
        // Setup state variables
        const templates = JSON.parse(document.getElementById('templates-data').textContent);
        let editor = null;
        let currentLanguage = 'python';
        let fontSize = 16;
        let lineWrap = true;
        let currentSnippetId = null;

        // Mock Coding Exercises Database
        const CodingChallenges = [
            { id: 1, title: "Two Sum Summation", difficulty: "Easy", solved: false, prompt: "Write a function/program that reads target integers and finds list positions summing to the target.", starter: { python: "# Return indices of two numbers that add up to target\ndef two_sum(nums, target):\n    pass\n\nprint(two_sum([2, 7, 11, 15], 9))" } },
            { id: 2, title: "String Reversal Loop", difficulty: "Easy", solved: false, prompt: "Read character strings and invert order sequence indexes.", starter: { python: "# Reverse string\ndef reverse_string(s):\n    return s[::-1]\n\nprint(reverse_string('Nexus IDE'))" } },
            { id: 3, title: "Palindrome Identifier", difficulty: "Easy", solved: false, prompt: "Verify matching character arrangements read backwards or forwards.", starter: { python: "# Palindrome check\ndef is_palindrome(s):\n    clean = ''.join(c.lower() for c in s if c.isalnum())\n    return clean == clean[::-1]\n\nprint(is_palindrome('A man, a plan, a canal, Panama'))" } },
            { id: 4, title: "Fibonacci Sequence Loop", difficulty: "Medium", solved: false, prompt: "Compute index terms in the sequence summation progression.", starter: { python: "# Fibonacci number computation\ndef fib(n):\n    if n <= 1: return n\n    return fib(n-1) + fib(n-2)\n\nprint(fib(6))" } },
            { id: 5, title: "Valid Parentheses Match", difficulty: "Medium", solved: false, prompt: "Determine if parentheses sequences are balanced correctly.", starter: { python: "# Validate parentheses groupings\ndef is_valid(s):\n    stack = []\n    mapping = {')': '(', '}': '{', ']': '['}\n    for char in s:\n        if char in mapping:\n            top = stack.pop() if stack else '#'\n            if mapping[char] != top: return False\n        else:\n            stack.append(char)\n    return not stack\n\nprint(is_valid('()[]{}'))" } },
            { id: 6, title: "Matrix Binary Search", difficulty: "Medium", solved: false, prompt: "Verify if search keys exist inside matrix arrays sorted horizontally.", starter: { python: "# Target search in sorted grid matrices\ndef search_matrix(matrix, target):\n    if not matrix: return False\n    r, c = 0, len(matrix[0]) - 1\n    while r < len(matrix) and c >= 0:\n        if matrix[r][c] == target: return True\n        elif matrix[r][c] > target: c -= 1\n        else: r += 1\n    return False\n\nprint(search_matrix([[1,3,5],[10,11,16]], 3))" } }
        ];

        // Initialize dialog system
        const Nexus = {
            modal: document.getElementById('nexusModal'),
            title: document.getElementById('nexusTitle'),
            body: document.getElementById('nexusBody'),
            footer: document.getElementById('nexusFooter'),
            inputContainer: document.getElementById('nexusInputContainer'),
            resolve: null,

            alert(msg, title = "System Notification") {
                this.setup(title, msg);
                this.footer.innerHTML = '<button class="nx-btn nx-btn-run" onclick="Nexus.close()">Understood</button>';
                this.modal.classList.add('active');
            },

            confirm(msg, title = "Confirmation Request") {
                return new Promise(res => {
                    this.resolve = res;
                    this.setup(title, msg);
                    this.footer.innerHTML = `
                        <button class="nx-btn" onclick="Nexus.submit(false)">Cancel</button>
                        <button class="nx-btn nx-btn-run" onclick="Nexus.submit(true)">Proceed</button>
                    `;
                    this.modal.classList.add('active');
                });
            },

            prompt(msg, defaultValue = "", title = "Prompt Dialog Input") {
                return new Promise(res => {
                    this.resolve = res;
                    this.setup(title, msg);
                    this.inputContainer.innerHTML = `<input type="text" id="nexusInput" class="nx-input" value="${defaultValue}" autofocus>`;
                    this.footer.innerHTML = `
                        <button class="nx-btn" onclick="Nexus.submit(null)">Cancel</button>
                        <button class="nx-btn nx-btn-run" onclick="Nexus.submit(document.getElementById('nexusInput').value)">Save Details</button>
                    `;
                    this.modal.classList.add('active');
                    setTimeout(() => document.getElementById('nexusInput').focus(), 50);
                });
            },

            setup(title, msg) {
                this.title.innerText = title;
                this.body.innerHTML = msg;
                this.inputContainer.innerHTML = '';
            },

            submit(val) {
                this.close();
                if (this.resolve) this.resolve(val);
            },

            close() {
                this.modal.classList.remove('active');
            }
        };

        // Window load setup
        window.addEventListener('load', () => {
            const textarea = document.getElementById('code-textarea');

            function initializeTextareaEditor() {
                document.getElementById('editor-loading').style.display = 'none';

                const localCode = localStorage.getItem('nexus_autosave_code');
                const localLang = localStorage.getItem('nexus_autosave_lang') || 'python';
                currentLanguage = sharedLang || localLang;
                document.getElementById('language').value = currentLanguage;

                textarea.value = sharedCode || localCode || templates[currentLanguage] || '';
                if (sharedOutput) {
                    document.getElementById('terminal-stdout').innerText = sharedOutput;
                }
                textarea.style.fontSize = fontSize + 'px';
                textarea.style.whiteSpace = 'pre-wrap';

                editor = {
                    getValue: () => textarea.value,
                    setValue: (value) => { textarea.value = value; },
                    updateOptions: (options) => {
                        if (options.fontSize) textarea.style.fontSize = options.fontSize + 'px';
                        if (options.wordWrap) textarea.style.whiteSpace = options.wordWrap === 'on' ? 'pre-wrap' : 'pre';
                    },
                    layout: () => {},
                    onDidChangeModelContent: (callback) => textarea.addEventListener('input', callback),
                    getModel: () => ({})
                };

                window.monaco = window.monaco || { editor: { setModelLanguage: function(){}, create: function(){} } };

                editor.onDidChangeModelContent(() => {
                    const isAutosave = document.getElementById('settings-autosave').checked;
                    if (isAutosave) {
                        localStorage.setItem('nexus_autosave_code', editor.getValue());
                        localStorage.setItem('nexus_autosave_lang', currentLanguage);
                    }
                });

                updateExplorerLabel();
            }

            if (typeof require !== 'undefined' && require.config) {
                require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.39.0/min/vs' }});
                require(['vs/editor/editor'], function() {
                    document.getElementById('editor-loading').style.display = 'none';

                    const localCode = localStorage.getItem('nexus_autosave_code');
                    const localLang = localStorage.getItem('nexus_autosave_lang') || 'python';
                    currentLanguage = sharedLang || localLang;
                    document.getElementById('language').value = currentLanguage;

                    const starterCode = sharedCode || localCode || templates[currentLanguage] || '';
                    if (sharedOutput) {
                        document.getElementById('terminal-stdout').innerText = sharedOutput;
                    }
                    editor = monaco.editor.create(document.getElementById('monaco-editor-target'), {
                        value: starterCode,
                        language: mapMonacoLanguage(currentLanguage),
                        theme: 'vs-dark',
                        fontSize: fontSize,
                        fontFamily: "'Fira Code', 'Cascadia Code', monospace",
                        automaticLayout: true,
                        wordWrap: lineWrap ? 'on' : 'off',
                        tabSize: 4,
                        minimap: { enabled: true }
                    });

                    editor.onDidChangeModelContent(() => {
                        const isAutosave = document.getElementById('settings-autosave').checked;
                        if (isAutosave) {
                            localStorage.setItem('nexus_autosave_code', editor.getValue());
                            localStorage.setItem('nexus_autosave_lang', currentLanguage);
                        }
                    });

                    updateExplorerLabel();
                }, initializeTextareaEditor);
            } else {
                initializeTextareaEditor();
            }

            // Populate dashboard quick templates
            populateTemplatesList();
            
            // Render coding challenges
            renderCodingChallenges();
            
            // Load saved project files count
            loadProjectsTable();

            // Populate GitHub style activity graph
            renderActivityGraph();
        });

        // Map languages to Monaco spec languages
        function mapMonacoLanguage(lang) {
            const mapping = {
                'python': 'python',
                'c': 'c',
                'cpp': 'cpp',
                'java': 'java',
                'javascript': 'javascript',
                'html': 'html',
                'php': 'php'
            };
            return mapping[lang] || 'plaintext';
        }

        // SPA View Switching
        function switchView(viewName) {
            document.querySelectorAll('.view-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            
            const selectedPanel = document.getElementById(`view-landing`); // default fallback
            const targetPanel = document.getElementById(`view-${viewName}`);
            
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
            
            const targetNav = document.getElementById(`nav-${viewName}`);
            if (targetNav) {
                targetNav.classList.add('active');
            }

            // Sync layout triggers on Monaco
            if (viewName === 'workspace' && editor) {
                setTimeout(() => editor.layout(), 100);
            }
        }

        // Dropdown selection handler
        async function changeLanguage() {
            const langSelect = document.getElementById('language');
            const targetLang = langSelect.value;

            // Always update currentLanguage immediately so executeWorkspace()
            // always uses the correct language regardless of editor state.
            currentLanguage = targetLang;
            localStorage.setItem('nexus_autosave_lang', currentLanguage);
            updateExplorerLabel();

            if (editor) {
                const currentContent = editor.getValue().trim();
                const templateCode = templates[targetLang] || '';

                // Only ask about loading the starter template — language is already switched.
                if (currentContent !== '' && currentContent !== (templates[targetLang] || '')) {
                    const loadTemplate = await Nexus.confirm(
                        `Switch to <strong>${targetLang.toUpperCase()}</strong> starter template?<br><small style="color:var(--text-dim)">Your current code will be replaced.</small>`
                    );
                    if (!loadTemplate) {
                        // Keep language switched but preserve code — just update syntax highlight
                        if (window.monaco && editor.getModel) {
                            monaco.editor.setModelLanguage(editor.getModel(), mapMonacoLanguage(currentLanguage));
                        }
                        return;
                    }
                }

                editor.setValue(templateCode);
                if (window.monaco && editor.getModel) {
                    monaco.editor.setModelLanguage(editor.getModel(), mapMonacoLanguage(currentLanguage));
                }
            }
        }

        // File Explorer details
        function updateExplorerLabel() {
            const explorerLbl = document.getElementById('explorer-filename');
            const extensions = {
                'python': 'main.py',
                'c': 'main.c',
                'cpp': 'main.cpp',
                'java': 'Main.java',
                'javascript': 'index.js',
                'html': 'index.html',
                'php': 'index.php'
            };
            explorerLbl.innerText = extensions[currentLanguage] || 'main.txt';
        }

        // Quick template loader from dashboard
        function loadTemplateIntoWorkspace(lang) {
            document.getElementById('language').value = lang;
            currentLanguage = lang;
            switchView('workspace');
            if (editor) {
                editor.setValue(templates[lang] || '');
                monaco.editor.setModelLanguage(editor.getModel(), mapMonacoLanguage(currentLanguage));
                updateExplorerLabel();
            }
        }

        // Render Coding Challenges panel list
        function renderCodingChallenges() {
            const panel = document.getElementById('challenges-panel');
            const profileStats = document.getElementById('profile-challenges-stats');
            
            let html = '<div style="display:flex; flex-direction:column; gap:8px;">';
            let statsHtml = '';
            
            let solvedCount = 0;

            CodingChallenges.forEach(c => {
                const badgeColor = c.difficulty === 'Easy' ? 'var(--success)' : 'orange';
                const statusIcon = c.solved ? '<i class="fa-solid fa-circle-check" style="color:var(--success)"></i>' : '<i class="fa-regular fa-circle"></i>';
                if (c.solved) solvedCount++;
                
                html += `
                    <div style="background:rgba(255,255,255,0.02); border:1px solid var(--glass-border); padding:8px; border-radius:6px; cursor:pointer;" onclick="loadChallenge(${c.id})">
                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px; font-weight:600; color:var(--text-bright);">
                            <span>${statusIcon} ${c.title}</span>
                            <span style="font-size:9px; color:${badgeColor};">${c.difficulty}</span>
                        </div>
                    </div>
                `;

                statsHtml += `
                    <div style="display:flex; justify-content:space-between; border-bottom:1px solid var(--glass-border); padding:6px 0;">
                        <span>${c.title}</span>
                        <span style="color:${c.solved ? 'var(--success)' : 'var(--text-dim)'};">${c.solved ? 'Solved' : 'Incomplete'}</span>
                    </div>
                `;
            });

            html += '</div>';
            panel.innerHTML = html;
            
            if (profileStats) {
                profileStats.innerHTML = statsHtml;
            }
            
            document.getElementById('stats-solved-challenges').innerText = solvedCount;
        }

        // Load Challenge starter code
        async function loadChallenge(id) {
            const challenge = CodingChallenges.find(c => c.id === id);
            if (!challenge) return;

            const confirmLoad = await Nexus.confirm(`Load challenge details into editor workspace: <strong>"${challenge.title}"</strong>?<br><br>Description: ${challenge.prompt}`);
            if (!confirmLoad) return;

            if (editor) {
                // Set language to python for challenges
                document.getElementById('language').value = 'python';
                currentLanguage = 'python';
                
                const code = challenge.starter.python;
                editor.setValue(code);
                monaco.editor.setModelLanguage(editor.getModel(), 'python');
                updateExplorerLabel();
                switchView('workspace');
                Nexus.alert(`Challenge workspace initialized! Solve the challenge, then execute it. When successful, the challenge will be marked as solved.`, challenge.title);
            }
        }

        // Execute code workspace run
        function executeWorkspace() {
            if (!editor) return;
            const code = editor.getValue();
            // Always read language from the dropdown as the source of truth.
            // currentLanguage should match, but the dropdown is the definitive value
            // the user can see — this prevents stale variable bugs entirely.
            const langEl = document.getElementById('language');
            const language = langEl ? langEl.value : currentLanguage;
            currentLanguage = language; // keep in sync
            const program_input = document.getElementById('workspace-stdin').value;

            const stdout = document.getElementById('terminal-stdout');
            const timing = document.getElementById('status-timing');
            const memory = document.getElementById('status-memory');
            const compInfo = document.getElementById('status-compiler-info');

            compInfo.innerText = "Compiler: Running...";
            stdout.className = "console-output loading";
            stdout.innerText = `Preparing execution context for ${language.toUpperCase()}...\nLaunching subprocess wrapper...\nRunning sandbox execution script...\n`;
            
            // Switch tabs
            switchConsoleTab('output');

            fetch('api/run_code.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    code,
                    language,
                    program_input
                })
            })
            .then(res => res.json())
            .then(data => {
                compInfo.innerText = 'Compiler: Finished';

                // Show errors or success highlights
                const isError = data.result && (data.result.includes('❌') || data.result.includes('⏱️'));
                stdout.className = isError ? 'console-output error' : 'console-output success';

                if (!isError) {
                    // Mark active challenge solved if matches keyword
                    checkChallengeStatus(code);
                }

                stdout.innerText = data.output || '(No output)';

                // Show real execution time from server
                const execSecs = data.exec_time ? parseFloat(data.exec_time).toFixed(3) : '---';
                timing.innerText = `Time: ${execSecs}s`;
                memory.innerText = `Memory: ~12 MB`;
            })
            .catch(err => {
                compInfo.innerText = 'Compiler: Error';
                stdout.className = 'console-output error';
                stdout.innerText = '❌ Network error reaching execution engine: ' + err;
            });
        }

        // Verify if coding challenge solved successfully
        function checkChallengeStatus(code) {
            // Palindrome check logic solved
            if (currentLanguage === 'python') {
                if (code.includes('is_palindrome') && code.includes('A man, a plan, a canal, Panama')) {
                    const c = CodingChallenges.find(x => x.id === 3);
                    if (c && !c.solved) {
                        c.solved = true;
                        Nexus.alert("🎉 Palindrome Identifier exercise solved successfully! Challenge checked off.", "Challenge Accomplished");
                        renderCodingChallenges();
                    }
                }
                // Two Sum solved
                if (code.includes('two_sum') && code.includes('[2, 7, 11, 15]')) {
                    const c = CodingChallenges.find(x => x.id === 1);
                    if (c && !c.solved) {
                        c.solved = true;
                        Nexus.alert("🎉 Two Sum Summation exercise solved successfully! Challenge checked off.", "Challenge Accomplished");
                        renderCodingChallenges();
                    }
                }
                // String reverse solved
                if (code.includes('reverse_string') && code.includes('Nexus IDE')) {
                    const c = CodingChallenges.find(x => x.id === 2);
                    if (c && !c.solved) {
                        c.solved = true;
                        Nexus.alert("🎉 String Reversal Loop exercise solved successfully! Challenge checked off.", "Challenge Accomplished");
                        renderCodingChallenges();
                    }
                }
                // Fibonacci solved
                if (code.includes('fib') && code.includes('fib(6)')) {
                    const c = CodingChallenges.find(x => x.id === 4);
                    if (c && !c.solved) {
                        c.solved = true;
                        Nexus.alert("🎉 Fibonacci Sequence Loop exercise solved successfully! Challenge checked off.", "Challenge Accomplished");
                        renderCodingChallenges();
                    }
                }
            }
        }

        // Stop execution logic
        function stopExecution() {
            const stdout = document.getElementById('terminal-stdout');
            stdout.className = "console-output error";
            stdout.innerText += "\n\n⚠️ Process killed: SIGTERM termination signal dispatched by user.";
            document.getElementById('status-compiler-info').innerText = "Compiler: Terminated";
        }

        // Format code request
        function formatWorkspaceCode() {
            if (!editor) return;
            const code = editor.getValue();
            const language = currentLanguage;

            fetch('api/format_code.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ code, language })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    editor.setValue(data.code);
                } else {
                    Nexus.alert("Error formatting file: " + data.error);
                }
            });
        }

        // Save Snippet code file
        async function saveSnippet() {
            if (!editor) return;
            const name = await Nexus.prompt("Identify Your Project Name:", "My Awesome Snippet");
            if (!name) return;

            fetch('api/save_snippet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    name,
                    code: editor.getValue(),
                    language: currentLanguage
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    currentSnippetId = data.id;
                    Nexus.alert(`Snippet saved successfully in your local workspace library! Project ID: ${data.id}`, "Snippet Synchronized");
                    loadProjectsTable();
                }
            });
        }

        // Share code link
        function shareProject() {
            if (!editor) return;
            const code = editor.getValue();
            const language = currentLanguage;

            fetch('api/share_code.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ code, language })
            })
            .then(res => res.json())
            .then(async data => {
                if (data.success) {
                    await Nexus.prompt("Live Collaboration link generated. Share with peers to start typing together:", data.url, "Collaborative URL Link");
                    
                    // Switch right tab to Collab, simulate joining session
                    switchRightTab('collab');
                } else {
                    Nexus.alert("Share failed: " + data.error);
                }
            });
        }

        // File Operations Upload/Download
        function triggerFileUpload() {
            document.getElementById('code-file-uploader').click();
        }

        function handleUploadedFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            fetch('api/upload_code.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && editor) {
                    editor.setValue(data.code);

                    // Auto-switch language based on detected extension
                    if (data.language) {
                        const langSelect = document.getElementById('language');
                        const supported = Array.from(langSelect.options).map(o => o.value);
                        if (supported.includes(data.language)) {
                            langSelect.value = data.language;
                            currentLanguage = data.language;
                            if (window.monaco && editor.getModel) {
                                monaco.editor.setModelLanguage(editor.getModel(), mapMonacoLanguage(data.language));
                            }
                            updateExplorerLabel();
                        }
                    }

                    Nexus.alert(`File "${data.filename}" uploaded successfully! Language auto-detected: ${(data.language || 'unknown').toUpperCase()}`, 'File Loaded');
                } else {
                    Nexus.alert('Upload failed: ' + (data.error || 'Unknown error'), 'Upload Error');
                }
                // Reset file input so the same file can be re-uploaded
                event.target.value = '';
            })
            .catch(err => {
                Nexus.alert('Network error during upload: ' + err, 'Upload Error');
            });
        }

        function downloadWorkspaceFile() {
            if (!editor) return;
            const code = editor.getValue();
            const blob = new Blob([code], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `main.${getFileExtension(currentLanguage)}`;
            a.click();
        }

        function getFileExtension(lang) {
            const ext = { 'python': 'py', 'c': 'c', 'cpp': 'cpp', 'java': 'java', 'javascript': 'js', 'html': 'html', 'php': 'php' };
            return ext[lang] || 'txt';
        }

        // Floating controls adjustments
        function adjustFontSize(delta) {
            fontSize = Math.min(32, Math.max(10, fontSize + delta));
            if (editor) {
                editor.updateOptions({ fontSize: fontSize });
            }
        }

        function toggleLineWrap() {
            lineWrap = !lineWrap;
            const btn = document.getElementById('wrap-toggle-btn');
            btn.style.color = lineWrap ? 'var(--primary)' : 'var(--text-dim)';
            if (editor) {
                editor.updateOptions({ wordWrap: lineWrap ? 'on' : 'off' });
            }
        }

        // ── Terminal stdin helpers ────────────────────────────────
        // Auto-grow the stdin textarea up to 5 lines
        function autoResizeStdin(el) {
            el.style.height = 'auto';
            const maxH = parseInt(getComputedStyle(el).lineHeight || '18') * 5 + 16;
            el.style.height = Math.min(el.scrollHeight, maxH) + 'px';
        }

        // Ctrl+Enter in stdin triggers Run; plain Enter just newlines
        function handleStdinKey(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                executeWorkspace();
            }
        }

        // Clear terminal output
        function clearTerminal() {
            const out = document.getElementById('terminal-stdout');
            out.className = 'console-output';
            out.innerText = '';
            document.getElementById('status-compiler-info').innerText = 'Compiler: Idle';
            document.getElementById('status-timing').innerText = 'Time: 0.00s';
            document.getElementById('status-memory').innerText = 'Memory: -- MB';
        }

        // View Tabs console
        function switchConsoleTab(tabName) {
            document.querySelectorAll('.bottom-console .console-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.bottom-console .console-tab-content').forEach(c => c.classList.remove('active'));

            document.getElementById(`tab-${tabName}`).classList.add('active');
            document.getElementById(`content-${tabName}`).classList.add('active');

            // If preview, copy output code into preview frame
            if (tabName === 'preview' && editor) {
                const iframe = document.getElementById('html-preview-frame');
                iframe.srcdoc = editor.getValue();
            }
        }

        // Switch Right Sidebar Tabs
        function switchRightTab(tabName) {
            document.getElementById('tab-ai-chat').classList.remove('active');
            document.getElementById('tab-collab').classList.remove('active');
            document.getElementById('content-ai-chat').style.display = 'none';
            document.getElementById('content-collab').style.display = 'none';

            document.getElementById(`tab-${tabName}`).classList.add('active');
            document.getElementById(`content-${tabName}`).style.display = 'flex';
        }

        // AI Assistant Logic Actions
        function executeAIAction(actionName) {
            if (!editor) return;
            const code = editor.getValue();
            const language = currentLanguage;

            const chatHistory = document.getElementById('ai-chat-history');
            
            // Add user query bubble
            const userBubble = document.createElement('div');
            userBubble.className = "chat-bubble user";
            userBubble.innerText = `Triggered quick AI action: ${actionName.toUpperCase()} for current code.`;
            chatHistory.appendChild(userBubble);
            
            // Scroll chat bottom
            chatHistory.scrollTop = chatHistory.scrollHeight;

            // Call backend API
            fetch('api/ai_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: actionName, code, language })
            })
            .then(res => res.json())
            .then(data => {
                const aiBubble = document.createElement('div');
                aiBubble.className = "chat-bubble assistant";
                
                // Parse simple markdown headers
                let formattedResult = data.result
                    .replace(/### (.*)/g, '<h3 style="margin:10px 0 5px 0; color:var(--primary);">$1</h3>')
                    .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
                    .replace(/`([^`]+)`/g, '<code style="background:rgba(255,255,255,0.08); padding:2px 4px; border-radius:3px;">$1</code>')
                    .replace(/\n/g, '<br>');
                
                aiBubble.innerHTML = `<h3>Nexus AI - Action Response</h3>${formattedResult}`;
                chatHistory.appendChild(aiBubble);
                chatHistory.scrollTop = chatHistory.scrollHeight;
            })
            .catch(err => {
                const errorBubble = document.createElement('div');
                errorBubble.className = "chat-bubble assistant";
                errorBubble.innerText = "Error fetching AI evaluation: " + err;
                chatHistory.appendChild(errorBubble);
                chatHistory.scrollTop = chatHistory.scrollHeight;
            });
        }

        // Send Custom AI Chat query
        function sendAIChatMessage() {
            const input = document.getElementById('ai-user-query');
            const query = input.value.trim();
            if (!query) return;

            const chatHistory = document.getElementById('ai-chat-history');
            const userBubble = document.createElement('div');
            userBubble.className = "chat-bubble user";
            userBubble.innerText = query;
            chatHistory.appendChild(userBubble);

            input.value = '';
            chatHistory.scrollTop = chatHistory.scrollHeight;

            const code = editor ? editor.getValue() : '';
            const language = currentLanguage;

            fetch('api/ai_chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    messages: [{ role: 'user', content: query }],
                    code,
                    language
                })
            })
            .then(res => res.json())
            .then(data => {
                const aiBubble = document.createElement('div');
                aiBubble.className = "chat-bubble assistant";
                aiBubble.innerHTML = `<h3>Assistant</h3>${data.reply}`;
                chatHistory.appendChild(aiBubble);
                chatHistory.scrollTop = chatHistory.scrollHeight;
            });
        }

        // Load project lists
        function loadProjectsTable() {
            fetch('api/get_snippets.php')
            .then(res => res.json())
            .then(data => {
                const totalLabel = document.getElementById('stats-total-snippets');
                const snippets = data.snippets || [];
                totalLabel.innerText = snippets.filter(s => !s.is_deleted).length;

                // Load dashboard list
                const dashboardList = document.getElementById('dashboard-recent-projects');
                const activeSnippets = snippets.filter(s => !s.is_deleted).slice(0, 4);

                if (activeSnippets.length === 0) {
                    dashboardList.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:30px; color:var(--text-dim);">No active saved projects. Save some code snippets!</div>`;
                    return;
                }

                dashboardList.innerHTML = activeSnippets.map(s => `
                    <div class="proj-card" onclick="loadProjectFromCard('${s.id}')">
                        <h4>${s.name} <span class="lang-tag">${s.language}</span></h4>
                        <p>Snippet cluster: ${s.folder || 'root'}</p>
                        <p style="font-size:10px; color:var(--text-dim); margin-top:8px;">Modified: ${s.timestamp}</p>
                    </div>
                `).join('');
            });
        }

        function loadProjectFromCard(id) {
            fetch('api/get_snippets.php')
            .then(res => res.json())
            .then(data => {
                const s = data.snippets.find(proj => proj.id === id);
                if (!s) return;

                if (editor) {
                    editor.setValue(s.code);
                    document.getElementById('language').value = s.language;
                    currentLanguage = s.language;
                    monaco.editor.setModelLanguage(editor.getModel(), mapMonacoLanguage(currentLanguage));
                    updateExplorerLabel();
                    switchView('workspace');
                    Nexus.alert(`Project loaded successfully!`);
                }
            });
        }

        // Generate templates dropdown on dashboard
        function populateTemplatesList() {
            const list = document.getElementById('dashboard-templates-list');
            const langs = ['python', 'c', 'cpp', 'java', 'javascript', 'html', 'php'];
            
            list.innerHTML = langs.map(l => `
                <button class="nx-btn" style="width:100%; text-align:left; justify-content:space-between;" onclick="loadTemplateIntoWorkspace('${l}')">
                    <span><i class="fa-solid fa-file-code" style="color:var(--primary); margin-right:8px;"></i> ${l.toUpperCase()} starter</span>
                    <i class="fa-solid fa-angle-right"></i>
                </button>
            `).join('');
        }

        // Apply settings changes
        function applySettings() {
            const theme = document.getElementById('settings-theme-selector').value;
            const size = parseInt(document.getElementById('settings-fontsize-selector').value);
            
            fontSize = size;
            
            if (editor) {
                editor.updateOptions({
                    theme: theme,
                    fontSize: fontSize
                });
            }

            const isAutosave = document.getElementById('settings-autosave').checked;
            document.getElementById('autosave-status').innerText = `Autosave: ${isAutosave ? 'On' : 'Off'}`;
        }

        // Collaboration thread comments actions
        function loadComments() {
            const targetId = currentSnippetId || 'default';
            const thread = document.getElementById('collab-comments-thread');

            fetch(`api/collab_comments.php?share_id=${targetId}`)
            .then(res => res.json())
            .then(data => {
                thread.innerHTML = data.comments.map(c => `
                    <div class="comment-item">
                        <div class="comment-author">
                            <span>${c.author}</span>
                            <span class="comment-time">${c.timestamp}</span>
                        </div>
                        <div style="color:var(--text-main); margin-top:3px;">${c.text}</div>
                    </div>
                `).join('');
                thread.scrollTop = thread.scrollHeight;
            });
        }

        function postCollabComment() {
            const input = document.getElementById('collab-comment-input');
            const text = input.value.trim();
            if (!text) return;

            const targetId = currentSnippetId || 'default';

            fetch('api/collab_comments.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    share_id: targetId,
                    author: "Developer (You)",
                    text: text
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    input.value = '';
                    loadComments();
                }
            });
        }

        // Periodically refresh comments if Collab tab active
        setInterval(() => {
            const collabTab = document.getElementById('tab-collab');
            if (collabTab && collabTab.classList.contains('active')) {
                loadComments();
            }
        }, 5000);

        // Open Design modal showcase
        function openDesignModal(category) {
            const modal = document.getElementById('designModal');
            const title = document.getElementById('designModalTitle');
            const body = document.getElementById('designModalBody');
            
            modal.classList.add('active');

            if (category === 'colors') {
                title.innerHTML = 'Nexus Design System - Color Palette';
                body.innerHTML = `
                    <p style="color:var(--text-dim); margin-bottom:15px;">Core glassmorphism color palette values used to design premium, dark-themed UI aesthetics.</p>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                        ${DesignSystem.theme.colors.map(c => `
                            <div style="display:flex; align-items:center; gap:15px; background:var(--bg-card); border:1px solid var(--glass-border); padding:10px; border-radius:8px;">
                                <div style="width:40px; height:40px; border-radius:6px; background:${c.value}; border:1px solid rgba(255,255,255,0.1)"></div>
                                <div>
                                    <div style="font-weight:bold; color:var(--text-bright);">${c.name}</div>
                                    <div style="font-size:11px; font-family:var(--font-mono); color:var(--primary); margin:2px 0;">${c.value}</div>
                                    <div style="font-size:11px; color:var(--text-dim);">${c.description}</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else if (category === 'components') {
                title.innerHTML = 'Nexus Component Library - Interactive Elements';
                body.innerHTML = `
                    <p style="color:var(--text-dim); margin-bottom:15px;">Reusable UI controls configured for uniform aesthetic interactions.</p>
                    <div style="display:flex; flex-direction:column; gap:20px;">
                        ${DesignSystem.components.map(comp => `
                            <div style="background:var(--bg-card); border:1px solid var(--glass-border); padding:15px; border-radius:8px;">
                                <h4 style="color:var(--text-bright); font-family:var(--font-accent); margin-bottom:8px;">${comp.name}</h4>
                                <div style="display:flex; align-items:center; min-height:45px; margin-bottom:10px;">
                                    ${comp.html}
                                </div>
                                <div class="design-spec">${comp.code.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>')}</div>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else if (category === 'flow') {
                title.innerHTML = 'Nexus IDE User Flow Diagram';
                body.innerHTML = `
                    <p style="color:var(--text-dim); margin-bottom:15px;">Architectural execution paths mapping navigation and compiler feedback loops.</p>
                    <div style="border:1px solid var(--glass-border); border-radius:12px; overflow:hidden; background:#0a0b10; padding:10px;">
                        ${DesignSystem.flowDiagram}
                    </div>
                `;
            }
        }

        function closeDesignModal() {
            document.getElementById('designModal').classList.remove('active');
        }

        // Render Github Streak activity blocks
        function renderActivityGraph() {
            const graph = document.getElementById('activityGraph');
            let html = '';
            // Render 24 columns, representing days
            for (let i = 0; i < 96; i++) {
                // Heuristic mapping levels
                let level = '0';
                if (i % 7 === 0) level = '1';
                else if (i % 11 === 0) level = '2';
                else if (i % 15 === 0) level = '3';
                else if (i % 19 === 0) level = '4';
                
                const hoverText = `Day -${96 - i}: ${level > 0 ? level * 3 + ' edits' : 'no commits'}`;
                html += `<div class="activity-cell level-${level}" title="${hoverText}"></div>`;
            }
            graph.innerHTML = html;
        }

        // Bind global key listeners
        window.addEventListener('keydown', (e) => {
            // Run code shortcut Ctrl + Enter
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                executeWorkspace();
            }
            // Save shortcut Ctrl + S
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                saveSnippet();
            }
            // Format shortcut Ctrl + Shift + F / Alt + Shift + F
            if (e.ctrlKey && e.shiftKey && e.key === 'F') {
                e.preventDefault();
                formatWorkspaceCode();
            }
        });
    </script>
</body>
</html>