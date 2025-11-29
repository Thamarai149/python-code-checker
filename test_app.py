#!/usr/bin/env python3
"""
Test script for the Multi-Language Online IDE
Tests basic functionality without running the full Flask app
"""

import subprocess
import sys

def test_flask_import():
    """Test if Flask is installed"""
    try:
        import flask
        try:
            from importlib.metadata import version
            flask_version = version("flask")
        except:
            flask_version = "installed"
        print("✅ Flask is installed (version: {})".format(flask_version))
        return True
    except ImportError:
        print("❌ Flask is not installed. Run: pip install flask")
        return False

def test_language(command, name):
    """Test if a language compiler/interpreter is available"""
    try:
        result = subprocess.run(
            command,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            timeout=5
        )
        print(f"✅ {name} is available")
        return True
    except (FileNotFoundError, subprocess.TimeoutExpired):
        print(f"❌ {name} is not available")
        return False
    except Exception as e:
        print(f"⚠️  {name} check failed: {e}")
        return False

def main():
    print("=" * 60)
    print("Multi-Language Online IDE - System Check")
    print("=" * 60)
    print()
    
    # Test Flask
    print("Checking Dependencies:")
    print("-" * 60)
    flask_ok = test_flask_import()
    print()
    
    # Test essential languages
    print("Checking Essential Languages:")
    print("-" * 60)
    essential = [
        (["python", "--version"], "Python"),
        (["node", "--version"], "Node.js (JavaScript)"),
        (["java", "-version"], "Java"),
        (["gcc", "--version"], "GCC (C)"),
        (["g++", "--version"], "G++ (C++)"),
    ]
    
    essential_count = 0
    for cmd, name in essential:
        if test_language(cmd, name):
            essential_count += 1
    
    print()
    print("Checking Additional Languages:")
    print("-" * 60)
    
    additional = [
        (["ruby", "--version"], "Ruby"),
        (["php", "--version"], "PHP"),
        (["go", "version"], "Go"),
        (["rustc", "--version"], "Rust"),
        (["perl", "--version"], "Perl"),
        (["Rscript", "--version"], "R"),
        (["kotlinc", "-version"], "Kotlin"),
        (["swift", "--version"], "Swift"),
        (["tsc", "--version"], "TypeScript"),
    ]
    
    additional_count = 0
    for cmd, name in additional:
        if test_language(cmd, name):
            additional_count += 1
    
    print()
    print("=" * 60)
    print("Summary:")
    print("-" * 60)
    print(f"Flask: {'✅ Ready' if flask_ok else '❌ Not installed'}")
    print(f"Essential Languages: {essential_count}/{len(essential)} available")
    print(f"Additional Languages: {additional_count}/{len(additional)} available")
    print()
    
    if flask_ok and essential_count >= 3:
        print("✅ System is ready! You can run: python app.py")
        return 0
    else:
        print("⚠️  Some components are missing. See INSTALLATION_GUIDE.md")
        return 1

if __name__ == "__main__":
    sys.exit(main())
