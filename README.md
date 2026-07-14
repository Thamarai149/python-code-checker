# 🚀 Nexus Studio - Multi-Language Online IDE

A premium, modern web-based compiler IDE and coding sandbox built with **HTML, CSS, PHP, and JavaScript**. Runs natively in **XAMPP** locally and deploys globally to **Render** using a custom **Docker container** hosting multiple compiler toolchains.

---

## ✨ Features

### 🖥️ Workspace IDE
- **Interactive Code Editor:** Powered by VS Code's Monaco Editor engine.
- **Auto-Save:** Instantly stores editor code variations in browser local storage.
- **Layout Adjustments:** Customizable editor themes (VS Code Dark, High Contrast, VS Code Light), font sizes, and line wrapping.
- **File Utilities:** Supports direct file imports (uploading code files) and exports (downloading code files).

### ⚙️ Multi-Language Execution Sandbox
- Executes programs securely with live subprocess monitoring, stdout capture, and custom inputs (stdin).
- Native execution support for **PHP**, **Python**, **JavaScript (Node.js)**, **C**, **C++**, and **Java** out-of-the-box.

### 🧠 Nexus AI Assistant
- **AI Action Suite:** 1-click evaluation buttons:
  - **Explain Code:** Deconstructs logic, loops, conditionals, and estimates time complexity.
  - **Optimize Code:** Inspects code and suggests modularity and speed improvements.
  - **AI Bug Fix:** Audits syntax rules (checks Python colons, C/C++/Java semicolons).
  - **Code Review:** Scores code safety, vulnerability check (`eval`, `exec`), and modularity.
- **Interactive AI Chat:** Ask algorithm questions or receive coding assistance in real-time.

### 👥 Live Collaboration
- **Live Link Sharing:** Click "Share" to generate public sandbox URLs.
- **Team Comments:** Real-time team message threads under shared projects.

---

## 🛠️ Project Structure

```
.
├── index.php               # Main UI workspace and layout coordinator
├── favicon.png             # Logo assets
├── css/
│   └── style.css           # Modern glassmorphic styles
├── js/
│   └── design_assets.js    # Interface animation configs
├── api/                    # Backend API endpoint scripts
│   ├── run_code.php        # Compiles & executes code via secure OS subprocesses
│   ├── save_snippet.php    # Saves snippets to file database
│   ├── get_snippets.php    # Retrieves stored snippets library
│   ├── format_code.php     # Beautifies code indentation
│   ├── share_code.php      # Registers shared code instances
│   ├── upload_code.php     # Parses file upload streams
│   ├── ai_action.php       # Processes quick AI assistance queries
│   ├── ai_chat.php         # Generates interactive AI chat responses
│   └── collab_comments.php # Registers and fetches team comments
├── data/                   # Persistent data directory
│   ├── snippets.json       # Snippet data store
│   └── comments.json       # Comment threads store
├── shared_codes/           # Stashes shared project files
├── uploads/                # Directory for user uploaded files
├── Dockerfile              # Container recipe for deployment (PHP, Python, Node, GCC)
└── render.yaml             # Render deployment configurations
```

---

## 🚀 Installation & Local Run

### Option 1: Local Run with XAMPP (Windows)
1. Make sure **XAMPP** is installed.
2. Place this project folder in your XAMPP directory:
   `C:\xampp\htdocs\Projects\python code checker\`
3. Start **Apache** service in the XAMPP Control Panel.
4. Navigate to the local workspace in your browser:
   [http://localhost/Projects/python%20code%20checker/](http://localhost/Projects/python%20code%20checker/)

*Note: For the execution engine to run languages other than PHP locally, those compilers/runtimes (Python, Node, GCC) must be installed on your Windows host system and added to your environment `PATH` variable.*

---

## ☁️ Deployment (Render)

This project is configured to run on Render via **Docker**. This allows Render to build a custom container pre-configured with all required compilers and libraries:

### The Docker Setup
Render compiles the container using the root **Dockerfile**, which:
- Starts from a `php:8.2-apache` Base Image.
- Installs **Python 3**, **Node.js/npm**, **GCC/G++**, and **Java JDK**.
- Automatically configures file permissions and exposes HTTP port 80.

To deploy or update:
1. Push your changes to GitHub.
2. Render detects the push, builds the container, and serves your app automatically.
