# 🚀 Multi-Language Online IDE

A powerful web-based IDE supporting 35 programming languages with syntax highlighting, code execution, and a modern interface inspired by OnlineGDB.

## ✨ Features

- **35 Programming Languages** supported
- **Syntax Highlighting** with CodeMirror
- **Dark/Light Theme** toggle
- **Execution History** tracking
- **Code Templates** for quick start
- **Execution Time** measurement
- **Download Output** functionality
- **Split-panel Layout** (editor + input/output)
- **Real-time Code Execution**

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

2. **Test Your System**
```bash
python test_app.py
```

3. **Run the Application**
```bash
python app.py
```

4. **Open in Browser**
```
http://127.0.0.1:5000
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

1. **Select Language**: Choose from the dropdown menu
2. **Write Code**: Use the code editor with syntax highlighting
3. **Add Input**: (Optional) Provide stdin input in the input panel
4. **Run Code**: Click the "▶ Run" button
5. **View Output**: See results in the output panel
6. **Check History**: Click "📜 History" to see recent executions

### Keyboard Shortcuts
- **Ctrl/Cmd + S**: Save output
- **Tab**: Indent code
- **Ctrl/Cmd + /**: Toggle comment (in some modes)

## 🎨 Features in Detail

### Code Templates
Click "📄 New" to load a Hello World template for the selected language.

### Dark Mode
Toggle between light and dark themes with the "🌙 Theme" button. Your preference is saved.

### Execution History
View your last 10 code executions with timestamps and success/failure indicators.

### Download Output
Save execution results to a text file with the "💾 Save Output" button.

### Execution Time
See how long your code takes to run (displayed in seconds).

## 🛠️ Project Structure

```
.
├── app.py                    # Flask application (main backend)
├── templates/
│   └── index.html           # Main UI template
├── static/
│   ├── style.css            # Additional styles
│   └── *.png                # Screenshots
├── test_app.py              # System check script
├── requirements.txt         # Python dependencies
├── start.bat                # Windows startup script
├── start.sh                 # Linux/macOS startup script
├── QUICKSTART.md            # Quick start guide
├── INSTALLATION_GUIDE.md    # Detailed installation guide
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
Change the port in `app.py` or kill the process using port 5000:
```bash
# Linux/macOS
lsof -ti:5000 | xargs kill -9

# Windows
netstat -ano | findstr :5000
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

**Made with ❤️ for developers who love coding in multiple languages!**
