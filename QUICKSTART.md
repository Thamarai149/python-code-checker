# 🚀 Quick Start Guide

Get up and running with the Multi-Language Online IDE in 3 simple steps!

## Step 1: Install Flask

```bash
pip install flask
```

Or use the requirements file:
```bash
pip install -r requirements.txt
```

## Step 2: Test Your System

Run the system check to see which languages are available:

```bash
python test_app.py
```

This will show you:
- ✅ Which languages are installed
- ❌ Which languages are missing
- Overall system readiness

## Step 3: Start the Application

### Option A: Use the startup script (Recommended)

**Windows:**
```cmd
start.bat
```

**Linux/macOS:**
```bash
chmod +x start.sh
./start.sh
```

### Option B: Run directly

```bash
python app.py
```

## Step 4: Open in Browser

Navigate to: **http://127.0.0.1:5000**

## 🎯 First Steps

1. **Select a Language** from the dropdown (try Python or JavaScript to start)
2. **Click "New"** to load a Hello World template
3. **Click "Run"** to execute the code
4. **See the output** in the right panel

## 🔧 Installing More Languages

If you want to add more languages, see the [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) for detailed instructions.

### Quick Install Commands

**Windows (PowerShell as Admin):**
```powershell
choco install -y python nodejs openjdk gcc mingw ruby php golang rust
```

**Linux (Ubuntu/Debian):**
```bash
sudo apt install -y python3 nodejs default-jdk gcc g++ ruby php-cli golang rustc
```

**macOS:**
```bash
brew install python node openjdk gcc ruby php go rust
```

## 🎨 Features to Try

- **Dark Mode**: Click the "Theme" button
- **History**: Click "History" to see past executions
- **Save Output**: Click "Save Output" to download results
- **Input**: Add stdin input in the Input panel

## ⚡ Keyboard Shortcuts

- **Ctrl/Cmd + Enter**: Submit form (run code)
- **Tab**: Indent in editor
- **Ctrl/Cmd + F**: Find in editor

## 🐛 Troubleshooting

### Port 5000 Already in Use

Edit `app.py` and change the port:
```python
if __name__ == "__main__":
    app.run(debug=True, port=8080)  # Change to any available port
```

### Language Not Working

1. Run `python test_app.py` to check installation
2. Verify the compiler/interpreter is in your PATH
3. See [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) for help

### Flask Not Found

```bash
pip install flask
```

Or:
```bash
pip install -r requirements.txt
```

## 📚 Next Steps

- Read the full [README.md](README.md) for all features
- Check [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) to install more languages
- Customize the code templates in `app.py`

## 🎉 You're Ready!

Start coding in 35 different programming languages! 🚀
