# 🚀 Multi-Language Online IDE

A powerful web-based IDE supporting 35 programming languages with syntax highlighting, code execution, and a modern interface inspired by OnlineGDB.

## ✨ Features

### Core Features
- **33+ Programming Languages** supported
- **Syntax Highlighting** with CodeMirror
- **Dark/Light Theme** toggle
- **Execution History** tracking
- **Code Templates** for quick start
- **Execution Time** measurement
- **Split-panel Layout** (editor + input/output)
- **Real-time Code Execution**

### 🆕 Online GDB Features (v2.0.0)
- **🔗 Code Sharing** - Generate shareable links for your code
- **📁 File Upload/Download** - Import and export code files
- **📚 Code Snippets Library** - Save and manage your favorite snippets
- **📊 Code Analysis** - Get statistics about your code
- **💾 Output Download** - Save execution results
- **📦 Project Export** - Export multiple files as ZIP

📖 **See [FEATURES.md](FEATURES.md) for complete feature documentation**

## 🌐 Supported Languages

### Compiled Languages
- C, C++, C#, Java, Kotlin, Scala
- Go, Rust, Swift, Objective-C
- Fortran, Cobol, Pascal, Assembly (NASM)
- Haskell, F#, Visual Basic

### Interpreted Languages
- Python, JavaScript (Node.js), TypeScript
- Ruby, PHP, Perl, Bash
- Lua, R, Dart

### Functional & Logic Languages
- Common Lisp, Scheme, Clojure
- Erlang, Elixir, Prolog

## 🚀 Quick Start

### Option 1: Use Startup Scripts (Easiest)

**Windows:**
```cmd
start.bat
```

**Linux/macOS:**
```bash
chmod +x start.sh
./start.sh
```

### Option 2: Manual Start

1. **Install Flask**
```bash
pip install -r requirements.txt
```


3. **Run the Application**
```bash
python app.py
```

4. **Open in Browser**
```
http://127.0.0.1:2006
```

📖 **See [QUICKSTART.md](QUICKSTART.md) for detailed first-time setup guide**

## 📦 Installation

### Check Installed Languages
Run the language checker to see which compilers/interpreters you have:
```bash
python check_languages.py
```

### Install All Languages

#### Linux (Ubuntu/Debian)
```bash
sudo apt update
sudo apt install -y python3 nodejs default-jdk gcc g++ ruby php-cli golang rustc \
    mono-complete perl r-base lua5.3 gfortran fpc open-cobol nasm \
    ghc haskell-platform sbcl guile-3.0 swi-prolog erlang elixir clojure scala

sudo snap install kotlin dart --classic
sudo npm install -g typescript
```

#### macOS
```bash
brew install python node openjdk gcc ruby php go rust mono kotlin swift perl r lua dart \
    fpc gnu-cobol gfortran nasm ghc haskell-stack sbcl guile swi-prolog erlang elixir \
    clojure scala typescript
```

#### Windows (PowerShell as Administrator)
```powershell
choco install -y python nodejs openjdk gcc mingw ruby php golang rust mono kotlin `
    strawberryperl r.project lua dart-sdk fpc gnucobol gfortran nasm haskell-dev `
    sbcl racket swi-prolog erlang elixir clojure scala dotnet-sdk

npm install -g typescript
```

### Minimal Installation (Top 10 Languages)
If you just want the most popular languages:
```bash
# Linux
sudo apt install python3 nodejs default-jdk gcc g++ ruby php-cli golang rustc
sudo npm install -g typescript

# macOS
brew install python node openjdk gcc ruby php go rust
npm install -g typescript

# Windows
choco install python nodejs openjdk gcc mingw ruby php golang rust
npm install -g typescript
```

## 📖 Detailed Installation Guide

For detailed installation instructions for each language, see [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md)

## 🎯 Usage

### Basic Usage
1. **Select Language**: Choose from the dropdown menu
2. **Write Code**: Use the code editor with syntax highlighting
3. **Add Input**: (Optional) Provide stdin input in the input panel
4. **Run Code**: Click the "▶ Run" button
5. **View Output**: See results in the output panel

### Advanced Features
- **🔗 Share Code**: Click "Share" to generate a shareable link
- **📁 Upload File**: Click "Upload" to import code from your computer
- **💾 Download**: Click "Download" to save your code
- **📚 Snippets**: Save frequently used code for quick access
- **📊 Analyze**: Get code statistics (lines, functions, classes)
- **🕒 History**: View your last 10 executions
- **🌙 Theme**: Toggle between light and dark modes

### Keyboard Shortcuts
- **Ctrl/Cmd + S**: Save output
- **Tab**: Indent code
- **Ctrl/Cmd + /**: Toggle comment (in some modes)

## 🎨 Features in Detail

### Code Templates
Click "📄 New" to load a Hello World template for the selected language.

### Code Sharing 🔗
Generate unique shareable links for your code. The link is automatically copied to your clipboard and can be shared with anyone.

### File Management 📁
- **Upload**: Import code files (`.py`, `.java`, `.js`, `.c`, `.cpp`, etc.)
- **Download**: Save your code with the correct file extension

### Code Snippets Library 📚
Save your frequently used code snippets with custom names. Access them anytime from the snippets sidebar.

### Code Analysis 📊
Get instant statistics about your code:
- Total lines and non-empty lines
- Character and word count
- Function and class detection
- Import statement counting

### Dark Mode 🌙
Toggle between light and dark themes with the "Theme" button. Your preference is saved.

### Execution History 🕒
View your last 10 code executions with timestamps and success/failure indicators.

### Execution Time ⏱️
See how long your code takes to run (displayed in seconds).

📖 **For detailed feature documentation, see [FEATURES.md](FEATURES.md)**

## 🛠️ Project Structure

```
.
├── app.py                    # Flask application (main backend)
├── templates/
│   └── index.html           # Main UI template
├── static/
│   ├── style.css            # Additional styles
│   └── *.png                # Screenshots
├── uploads/                  # Uploaded files directory
├── shared_codes/             # Shared code storage
├── test_app.py              # System check script
├── requirements.txt         # Python dependencies
├── start.bat                # Windows startup script
├── start.sh                 # Linux/macOS startup script
├── QUICKSTART.md            # Quick start guide
├── INSTALLATION_GUIDE.md    # Detailed installation guide
├── FEATURES.md              # Complete feature documentation
├── CHANGELOG.md             # Version history
├── PROJECT_STATUS.md        # Project status and roadmap
├── README.md                # This file
└── .gitignore               # Git ignore rules
```

## 🔧 Configuration

### Change Port
Edit `app.py`:
```python
if __name__ == "__main__":
    app.run(debug=True, port=8080)  # Change port here
```

### Change Secret Key
Edit `app.py`:
```python
app.secret_key = 'your-secret-key-here'  # Change for production
```

## 🐛 Troubleshooting

### Language Not Working
1. Check if the compiler/interpreter is installed:
   ```bash
   python check_languages.py
   ```
2. Verify the command is in your PATH
3. See [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) for installation help

### Port Already in Use
Change the port in `app.py` or kill the process using port 2006:
```bash
# Linux/macOS
lsof -ti:2006 | xargs kill -9

# Windows
netstat -ano | findstr :2006
taskkill /PID <PID> /F
```

### Permission Errors
- Linux/macOS: Run with `sudo` if needed
- Windows: Run PowerShell as Administrator

## 🤝 Contributing

Contributions are welcome! Feel free to:
- Add more languages
- Improve the UI
- Fix bugs
- Add features

## 📝 License

This project is open source and available under the MIT License.

## 🙏 Acknowledgments

- Inspired by [OnlineGDB](https://www.onlinegdb.com/)
- Built with [Flask](https://flask.palletsprojects.com/)
- Code editor powered by [CodeMirror](https://codemirror.net/)

## 📧 Support

For issues and questions:
- Check [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md)
- Run `python check_languages.py` to diagnose issues
- Refer to official documentation for each language

---

## 📚 Documentation

### Getting Started
- **[QUICKSTART.md](QUICKSTART.md)** - Quick start guide for beginners
- **[USER_GUIDE.md](USER_GUIDE.md)** - Complete walkthrough of all features
- **[INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md)** - Detailed language installation

### Features & Reference
- **[FEATURES.md](FEATURES.md)** - Complete feature list and usage guide
- **[QUICK_REFERENCE.md](QUICK_REFERENCE.md)** - Quick reference for all features
- **[COMPARISON.md](COMPARISON.md)** - Comparison with Online GDB

### Technical Documentation
- **[API_DOCUMENTATION.md](API_DOCUMENTATION.md)** - Complete API reference
- **[CHANGELOG.md](CHANGELOG.md)** - Version history and updates
- **[PROJECT_STATUS.md](PROJECT_STATUS.md)** - Project status and roadmap
- **[IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)** - Implementation details

## 🎯 Version

**Current Version**: 2.0.0  
**Release Date**: November 29, 2025  
**Status**: Production Ready - Feature Complete

---

**Made with ❤️ for developers who love coding in multiple languages!**
