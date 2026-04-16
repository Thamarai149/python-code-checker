# Language Packages Installation Guide

This guide will help you install all 35 programming language compilers and interpreters.

## Prerequisites
- Python 3.x
- Flask: `pip install flask`

---

## Installation by Operating System

### Windows

#### Package Managers
First, install a package manager:
- **Chocolatey**: https://chocolatey.org/install
- **Scoop**: https://scoop.sh/

#### Languages Installation (Windows)

```powershell
# Using Chocolatey
choco install python nodejs jdk11 gcc mingw ruby php golang rust

# Additional languages
choco install kotlin swift dotnet-sdk mono
choco install perl strawberryperl r.project
choco install haskell-dev lua dart-sdk

# Compilers
choco install fpc gnucobol gfortran nasm

# Functional languages
choco install sbcl racket clojure

# Using Scoop
scoop install python nodejs openjdk gcc ruby php go rust
scoop install kotlin perl r lua dart
```

---

### Linux (Ubuntu/Debian)

```bash
# Update package list
sudo apt update

# Core languages
sudo apt install -y python3 nodejs default-jdk gcc g++ ruby php-cli golang rustc

# Additional languages
sudo apt install -y mono-complete csharp-shell
sudo apt install -y perl r-base lua5.3 gfortran

# Compilers
sudo apt install -y fpc open-cobol nasm

# Functional languages
sudo apt install -y ghc haskell-platform sbcl guile-3.0
sudo apt install -y swi-prolog erlang elixir clojure

# Modern languages
sudo snap install kotlin --classic
sudo snap install dart --classic
sudo apt install -y scala

# TypeScript
sudo npm install -g typescript

# Swift (Linux)
wget https://swift.org/builds/swift-5.9-release/ubuntu2204/swift-5.9-RELEASE/swift-5.9-RELEASE-ubuntu22.04.tar.gz
tar xzf swift-5.9-RELEASE-ubuntu22.04.tar.gz
sudo mv swift-5.9-RELEASE-ubuntu22.04 /usr/share/swift
echo 'export PATH=/usr/share/swift/usr/bin:$PATH' >> ~/.bashrc
```

---

### macOS

```bash
# Install Homebrew first
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Core languages
brew install python node openjdk gcc ruby php go rust

# Additional languages
brew install mono kotlin swift perl r lua dart

# Compilers
brew install fpc gnu-cobol gfortran nasm

# Functional languages
brew install ghc haskell-stack sbcl guile
brew install swi-prolog erlang elixir clojure

# Modern languages
brew install scala typescript

# Bash (update to latest)
brew install bash
```

---

## Language-Specific Installation

### 1. **Java**
```bash
# Windows
choco install openjdk11

# Linux
sudo apt install default-jdk

# macOS
brew install openjdk@11
```

### 2. **Python**
```bash
# Windows
choco install python

# Linux
sudo apt install python3

# macOS
brew install python3
```

### 3. **JavaScript (Node.js)**
```bash
# Windows
choco install nodejs

# Linux
sudo apt install nodejs npm

# macOS
brew install node
```

### 4. **C/C++**
```bash
# Windows
choco install mingw

# Linux
sudo apt install gcc g++

# macOS
xcode-select --install
```

### 5. **C#**
```bash
# Windows
choco install dotnet-sdk

# Linux
sudo apt install mono-complete

# macOS
brew install mono
```

### 6. **Ruby**
```bash
# Windows
choco install ruby

# Linux
sudo apt install ruby-full

# macOS
brew install ruby
```

### 7. **PHP**
```bash
# Windows
choco install php

# Linux
sudo apt install php-cli

# macOS
brew install php
```

### 8. **Go**
```bash
# Windows
choco install golang

# Linux
sudo apt install golang-go

# macOS
brew install go
```

### 9. **Rust**
```bash
# All platforms
curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh
```

### 10. **Bash**
```bash
# Pre-installed on Linux/macOS
# Windows: Use Git Bash or WSL
```

### 11. **Perl**
```bash
# Windows
choco install strawberryperl

# Linux
sudo apt install perl

# macOS
brew install perl
```

### 12. **R**
```bash
# Windows
choco install r.project

# Linux
sudo apt install r-base

# macOS
brew install r
```

### 13. **Kotlin**
```bash
# Windows
choco install kotlin

# Linux
sudo snap install kotlin --classic

# macOS
brew install kotlin
```

### 14. **Swift**
```bash
# Windows
# Download from: https://www.swift.org/download/

# Linux
# See Linux section above

# macOS
# Pre-installed with Xcode
xcode-select --install
```

### 15. **Scala**
```bash
# Windows
choco install scala

# Linux
sudo apt install scala

# macOS
brew install scala
```

### 16. **Lua**
```bash
# Windows
choco install lua

# Linux
sudo apt install lua5.3

# macOS
brew install lua
```

### 17. **TypeScript**
```bash
# All platforms (requires Node.js)
npm install -g typescript
```

### 18. **Dart**
```bash
# Windows
choco install dart-sdk

# Linux
sudo snap install dart --classic

# macOS
brew install dart
```

### 19. **Fortran**
```bash
# Windows
choco install gfortran

# Linux
sudo apt install gfortran

# macOS
brew install gcc
```

### 20. **Cobol**
```bash
# Windows
choco install gnucobol

# Linux
sudo apt install open-cobol

# macOS
brew install gnu-cobol
```

### 21. **Pascal**
```bash
# Windows
choco install fpc

# Linux
sudo apt install fpc

# macOS
brew install fpc
```

### 22. **Haskell**
```bash
# Windows
choco install haskell-dev

# Linux
sudo apt install ghc haskell-platform

# macOS
brew install ghc haskell-stack
```

### 23. **Objective-C**
```bash
# Windows
# Requires GNUstep

# Linux
sudo apt install gobjc gnustep gnustep-devel

# macOS
# Pre-installed with Xcode
xcode-select --install
```

### 24. **Assembly (NASM)**
```bash
# Windows
choco install nasm

# Linux
sudo apt install nasm

# macOS
brew install nasm
```

### 25. **Prolog**
```bash
# Windows
choco install swi-prolog

# Linux
sudo apt install swi-prolog

# macOS
brew install swi-prolog
```

### 26. **Common Lisp**
```bash
# Windows
choco install sbcl

# Linux
sudo apt install sbcl

# macOS
brew install sbcl
```

### 27. **Scheme**
```bash
# Windows
choco install racket

# Linux
sudo apt install guile-3.0

# macOS
brew install guile
```

### 28. **Erlang**
```bash
# Windows
choco install erlang

# Linux
sudo apt install erlang

# macOS
brew install erlang
```

### 29. **Elixir**
```bash
# Windows
choco install elixir

# Linux
sudo apt install elixir

# macOS
brew install elixir
```

### 30. **Clojure**
```bash
# Windows
choco install clojure

# Linux
sudo apt install clojure

# macOS
brew install clojure
```

### 31. **F#**
```bash
# Windows
choco install dotnet-sdk

# Linux
sudo apt install dotnet-sdk-7.0

# macOS
brew install dotnet
```

### 32. **Visual Basic**
```bash
# Windows
choco install mono

# Linux
sudo apt install mono-vbnc

# macOS
brew install mono
```

---

## Verification

After installation, verify each language:

```bash
# Check versions
python --version
node --version
java --version
gcc --version
g++ --version
ruby --version
php --version
go version
rustc --version
kotlinc -version
swift --version
scala -version
lua -v
tsc --version
dart --version
gfortran --version
cobc --version
fpc -version
ghc --version
nasm -version
swipl --version
sbcl --version
guile --version
erl -version
elixir --version
clojure --version
dotnet --version
```

---

## Quick Install Scripts

### Linux (All-in-One)
```bash
#!/bin/bash
sudo apt update
sudo apt install -y python3 nodejs default-jdk gcc g++ ruby php-cli golang rustc \
    mono-complete perl r-base lua5.3 gfortran fpc open-cobol nasm \
    ghc haskell-platform sbcl guile-3.0 swi-prolog erlang elixir clojure scala

sudo snap install kotlin --classic
sudo snap install dart --classic
sudo npm install -g typescript
```

### macOS (All-in-One)
```bash
#!/bin/bash
brew install python node openjdk gcc ruby php go rust mono kotlin swift perl r lua dart \
    fpc gnu-cobol gfortran nasm ghc haskell-stack sbcl guile swi-prolog erlang elixir \
    clojure scala typescript
```

### Windows (All-in-One PowerShell)
```powershell
# Run as Administrator
choco install -y python nodejs openjdk gcc mingw ruby php golang rust mono kotlin `
    strawberryperl r.project lua dart-sdk fpc gnucobol gfortran nasm haskell-dev `
    sbcl racket swi-prolog erlang elixir clojure scala dotnet-sdk

npm install -g typescript
```

---

## Troubleshooting

### Common Issues

1. **Command not found**: Add the installation directory to your PATH
2. **Permission denied**: Run with sudo (Linux/macOS) or as Administrator (Windows)
3. **Package not available**: Update your package manager or use alternative sources

### PATH Configuration

**Windows:**
```powershell
# Add to PATH
setx PATH "%PATH%;C:\path\to\compiler"
```

**Linux/macOS:**
```bash
# Add to ~/.bashrc or ~/.zshrc
export PATH=$PATH:/path/to/compiler
```

---

## Minimal Installation

If you want to start with the most common languages:

```bash
# Essential 10 languages
# Python, JavaScript, Java, C, C++, Ruby, PHP, Go, Rust, TypeScript

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

---

## Docker Alternative

If you prefer not to install everything locally, you can use Docker:

```dockerfile
# Create a Dockerfile with all languages
FROM ubuntu:22.04

RUN apt-get update && apt-get install -y \
    python3 nodejs default-jdk gcc g++ ruby php-cli golang rustc \
    mono-complete perl r-base lua5.3 gfortran fpc open-cobol nasm \
    ghc sbcl guile-3.0 swi-prolog erlang elixir clojure scala

# Add more as needed
```

---

## Support

For language-specific issues, refer to:
- Official documentation for each language
- Stack Overflow
- Language-specific forums and communities

---

**Note:** Some languages may require additional configuration or dependencies. Always refer to the official documentation for the most up-to-date installation instructions.
