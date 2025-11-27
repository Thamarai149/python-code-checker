#!/usr/bin/env python3
"""
Language Availability Checker
Checks which programming language compilers/interpreters are installed
"""

import subprocess
import sys

# Language check commands
LANGUAGES = {
    "Python": ["python", "--version"],
    "Java": ["java", "-version"],
    "JavaScript (Node.js)": ["node", "--version"],
    "C (GCC)": ["gcc", "--version"],
    "C++ (G++)": ["g++", "--version"],
    "C# (Mono)": ["csc", "/help"],
    "Ruby": ["ruby", "--version"],
    "PHP": ["php", "--version"],
    "Go": ["go", "version"],
    "Rust": ["rustc", "--version"],
    "Bash": ["bash", "--version"],
    "Perl": ["perl", "--version"],
    "R": ["Rscript", "--version"],
    "Kotlin": ["kotlinc", "-version"],
    "Swift": ["swift", "--version"],
    "Scala": ["scala", "-version"],
    "Lua": ["lua", "-v"],
    "TypeScript": ["tsc", "--version"],
    "Dart": ["dart", "--version"],
    "Fortran": ["gfortran", "--version"],
    "Cobol": ["cobc", "--version"],
    "Pascal": ["fpc", "-version"],
    "Haskell": ["ghc", "--version"],
    "Objective-C (GCC)": ["gcc", "--version"],
    "Assembly (NASM)": ["nasm", "-version"],
    "Prolog (SWI)": ["swipl", "--version"],
    "Common Lisp (SBCL)": ["sbcl", "--version"],
    "Scheme (Guile)": ["guile", "--version"],
    "Erlang": ["erl", "-version"],
    "Elixir": ["elixir", "--version"],
    "Clojure": ["clojure", "--version"],
    "F#": ["dotnet", "fsi", "--version"],
    "Visual Basic (Mono)": ["vbnc", "--version"],
}

def check_language(name, command):
    """Check if a language is installed"""
    try:
        result = subprocess.run(
            command,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            timeout=5
        )
        return True
    except (FileNotFoundError, subprocess.TimeoutExpired):
        return False
    except Exception:
        return False

def main():
    print("=" * 70)
    print("PROGRAMMING LANGUAGE AVAILABILITY CHECKER")
    print("=" * 70)
    print()
    
    installed = []
    not_installed = []
    
    for name, command in LANGUAGES.items():
        status = check_language(name, command)
        if status:
            installed.append(name)
            print(f"✅ {name:<35} INSTALLED")
        else:
            not_installed.append(name)
            print(f"❌ {name:<35} NOT FOUND")
    
    print()
    print("=" * 70)
    print(f"SUMMARY: {len(installed)}/{len(LANGUAGES)} languages installed")
    print("=" * 70)
    print()
    
    if not_installed:
        print("Missing Languages:")
        for lang in not_installed:
            print(f"  - {lang}")
        print()
        print("📖 See INSTALLATION_GUIDE.md for installation instructions")
    else:
        print("🎉 All languages are installed! You're ready to go!")
    
    print()
    
    # Installation suggestions
    if not_installed and len(not_installed) > 5:
        print("Quick Install Commands:")
        print()
        
        if sys.platform == "linux":
            print("Linux (Ubuntu/Debian):")
            print("  sudo apt update")
            print("  sudo apt install -y python3 nodejs default-jdk gcc g++ ruby \\")
            print("    php-cli golang rustc mono-complete perl r-base lua5.3 \\")
            print("    gfortran fpc open-cobol nasm ghc sbcl guile-3.0 \\")
            print("    swi-prolog erlang elixir clojure scala")
            print("  sudo snap install kotlin dart --classic")
            print("  sudo npm install -g typescript")
        
        elif sys.platform == "darwin":
            print("macOS:")
            print("  brew install python node openjdk gcc ruby php go rust \\")
            print("    mono kotlin swift perl r lua dart fpc gnu-cobol \\")
            print("    gfortran nasm ghc sbcl guile swi-prolog erlang \\")
            print("    elixir clojure scala typescript")
        
        elif sys.platform == "win32":
            print("Windows (PowerShell as Administrator):")
            print("  choco install -y python nodejs openjdk gcc mingw ruby \\")
            print("    php golang rust mono kotlin strawberryperl r.project \\")
            print("    lua dart-sdk fpc gnucobol gfortran nasm haskell-dev \\")
            print("    sbcl racket swi-prolog erlang elixir clojure scala dotnet-sdk")
            print("  npm install -g typescript")

if __name__ == "__main__":
    main()
