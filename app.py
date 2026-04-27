from flask import Flask, render_template, request, session, jsonify, send_file, url_for
import subprocess, tempfile, os
import time
from datetime import datetime
import json
import hashlib
import io
import zipfile
import uuid
from werkzeug.utils import secure_filename

app = Flask(__name__)
app.secret_key = 'your-secret-key-here-change-in-production'
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024  # 16MB max file size
app.config['UPLOAD_FOLDER'] = 'uploads'

# Create uploads directory if it doesn't exist
os.makedirs('uploads', exist_ok=True)
os.makedirs('shared_codes', exist_ok=True)

# Code templates for each language
CODE_TEMPLATES = {
    "java": """public class Main {
    public static void main(String[] args) {
        System.out.println("Hello, Java!");
    }
}""",
    "python": """# Python code
print("Hello, Python!")""",
    "javascript": """// JavaScript code
console.log("Hello, JavaScript!");""",
    "c": """#include <stdio.h>

int main() {
    printf("Hello, C!\\n");
    return 0;
}""",
    "cpp": """#include <iostream>
using namespace std;

int main() {
    cout << "Hello, C++!" << endl;
    return 0;
}""",
    "csharp": """using System;

class Program {
    static void Main() {
        Console.WriteLine("Hello, C#!");
    }
}""",
    "ruby": """# Ruby code
puts "Hello, Ruby!\"""",
    "php": """<?php
echo "Hello, PHP!\\n";
?>""",
    "go": """package main

import "fmt"

func main() {
    fmt.Println("Hello, Go!")
}""",
    "rust": """fn main() {
    println!("Hello, Rust!");
}""",
    "bash": """#!/bin/bash
echo "Hello, Bash!\"""",
    "perl": """#!/usr/bin/perl
print "Hello, Perl!\\n";""",
    "r": """# R code
print("Hello, R!")""",
    "kotlin": """fun main() {
    println("Hello, Kotlin!")
}""",
    "swift": """import Foundation
print("Hello, Swift!")""",
    "scala": """object Main {
  def main(args: Array[String]): Unit = {
    println("Hello, Scala!")
  }
}""",
    "lua": """print("Hello, Lua!")""",
    "typescript": """// TypeScript code
console.log("Hello, TypeScript!");""",
    "dart": """void main() {
  print('Hello, Dart!');
}""",
    "fortran": """program hello
  print *, 'Hello, Fortran!'
end program hello""",
    "cobol": """       IDENTIFICATION DIVISION.
       PROGRAM-ID. HELLO.
       PROCEDURE DIVISION.
           DISPLAY 'Hello, Cobol!'.
           STOP RUN.""",
    "pascal": """program Hello;
begin
  writeln('Hello, Pascal!');
end.""",
    "haskell": """main :: IO ()
main = putStrLn "Hello, Haskell!\"""",
    "objectivec": """#import <Foundation/Foundation.h>

int main() {
    @autoreleasepool {
        NSLog(@"Hello, Objective-C!");
    }
    return 0;
}""",
    "assembly": """section .data
    msg db 'Hello, Assembly!', 0xA
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
    int 0x80""",
    "prolog": """% Prolog code
:- initialization(main).
main :- write('Hello, Prolog!'), nl, halt.""",
    "lisp": """(format t "Hello, Common Lisp!~%")""",
    "scheme": """(display "Hello, Scheme!")
(newline)""",
    "erlang": """-module(hello).
-export([start/0]).

start() ->
    io:format("Hello, Erlang!~n").""",
    "elixir": """IO.puts "Hello, Elixir!\"""",
    "clojure": """(println "Hello, Clojure!")""",
    "fsharp": """printfn "Hello, F#!\"""",
    "vb": """Module Hello
    Sub Main()
        Console.WriteLine("Hello, Visual Basic!")
    End Sub
End Module"""
}

def check_and_run_code(language, code, program_input):
    start_time = time.time()
    TIMEOUT = 30  # 30 seconds timeout for all executions
    
    # Toolchain configuration (Force 64-bit MinGW-w64)
    env = os.environ.copy()
    mingw64_path = r"C:\ProgramData\mingw64\mingw64\bin"
    env["PATH"] = mingw64_path + ";" + env["PATH"]
    gcc_path = os.path.join(mingw64_path, "gcc.exe")
    gpp_path = os.path.join(mingw64_path, "g++.exe")
    if language == "java":
        with tempfile.TemporaryDirectory() as tmpdir:
            java_file = os.path.join(tmpdir, "Main.java")
            with open(java_file, "w") as f:
                f.write(code)

            # Compile
            compile_proc = subprocess.run(
                ["javac", java_file],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            if compile_proc.returncode != 0:
                exec_time = time.time() - start_time
                return compile_proc.stderr, None, exec_time

            # Run
            try:
                run_proc = subprocess.run(
                    ["java", "-cp", tmpdir, "Main"],
                    input=program_input,
                    stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, timeout=TIMEOUT
                )
                exec_time = time.time() - start_time
                return "✅ Java code is syntactically correct.", run_proc.stdout if run_proc.stdout else run_proc.stderr, exec_time
            except subprocess.TimeoutExpired:
                exec_time = time.time() - start_time
                return "❌ Execution timeout (30 seconds)", None, exec_time

    elif language == "python":
        tmpfile_path = None
        try:
            compile(code, "<string>", "exec")  # Syntax check
            with tempfile.NamedTemporaryFile(delete=False, suffix=".py") as tmpfile:
                tmpfile.write(code.encode("utf-8"))
                tmpfile_path = tmpfile.name

            run_proc = subprocess.run(
                ["python", tmpfile_path],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            os.unlink(tmpfile_path)  # Clean up temp file
            return "✅ Python code is syntactically correct.", run_proc.stdout if run_proc.stdout else run_proc.stderr, exec_time
        except SyntaxError as e:
            exec_time = time.time() - start_time
            return str(e), None, exec_time
        finally:
            if tmpfile_path and os.path.exists(tmpfile_path):
                try:
                    os.unlink(tmpfile_path)
                except:
                    pass

    elif language == "javascript":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".js") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        try:
            run_proc = subprocess.run(
                ["node", tmpfile_path],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, timeout=30
            )
            exec_time = time.time() - start_time
            if run_proc.returncode == 0:
                return "✅ JavaScript code is syntactically correct.", run_proc.stdout, exec_time
            else:
                return run_proc.stderr, None, exec_time
        except subprocess.TimeoutExpired:
            exec_time = time.time() - start_time
            return "❌ Execution timeout (30 seconds)", None, exec_time
        finally:
            if os.path.exists(tmpfile_path):
                os.unlink(tmpfile_path)

    elif language == "c":
        with tempfile.TemporaryDirectory() as tmpdir:
            c_file = os.path.join(tmpdir, "program.c")
            exe_file = os.path.join(tmpdir, "program.exe")
            with open(c_file, "w") as f:
                f.write(code)

            # Compile
            compile_proc = subprocess.run(
                [gcc_path, c_file, "-o", exe_file],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, env=env
            )
            if compile_proc.returncode != 0:
                exec_time = time.time() - start_time
                return "❌ Compilation Error:\n" + compile_proc.stderr, None, exec_time

            # Run
            try:
                run_proc = subprocess.run(
                    [exe_file],
                    input=program_input,
                    stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, timeout=TIMEOUT, env=env
                )
                exec_time = time.time() - start_time
                return "✅ C code is syntactically correct.", run_proc.stdout if run_proc.stdout else run_proc.stderr, exec_time
            except subprocess.TimeoutExpired:
                exec_time = time.time() - start_time
                return "❌ Execution timeout (30 seconds)", None, exec_time
            except Exception as e:
                exec_time = time.time() - start_time
                return "❌ Execution Error: " + str(e), None, exec_time

    elif language == "cpp":
        with tempfile.TemporaryDirectory() as tmpdir:
            cpp_file = os.path.join(tmpdir, "program.cpp")
            exe_file = os.path.join(tmpdir, "program.exe")
            with open(cpp_file, "w") as f:
                f.write(code)

            # Compile
            compile_proc = subprocess.run(
                [gpp_path, cpp_file, "-o", exe_file],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, env=env
            )
            if compile_proc.returncode != 0:
                exec_time = time.time() - start_time
                return "❌ Compilation Error:\n" + compile_proc.stderr, None, exec_time

            # Run
            try:
                run_proc = subprocess.run(
                    [exe_file],
                    input=program_input,
                    stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, timeout=TIMEOUT, env=env
                )
                exec_time = time.time() - start_time
                return "✅ C++ code is syntactically correct.", run_proc.stdout if run_proc.stdout else run_proc.stderr, exec_time
            except subprocess.TimeoutExpired:
                exec_time = time.time() - start_time
                return "❌ Execution timeout (30 seconds)", None, exec_time
            except Exception as e:
                exec_time = time.time() - start_time
                return "❌ Execution Error: " + str(e), None, exec_time

    elif language == "csharp":
        with tempfile.TemporaryDirectory() as tmpdir:
            cs_file = os.path.join(tmpdir, "Program.cs")
            exe_file = os.path.join(tmpdir, "Program.exe")
            with open(cs_file, "w") as f:
                f.write(code)

            compile_proc = subprocess.run(
                ["csc", "/out:" + exe_file, cs_file],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, env=env
            )
            if compile_proc.returncode != 0:
                exec_time = time.time() - start_time
                return compile_proc.stderr, None, exec_time

            run_proc = subprocess.run(
                [exe_file],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, env=env
            )
            exec_time = time.time() - start_time
            return "✅ C# code is syntactically correct.", run_proc.stdout if run_proc.stdout else run_proc.stderr, exec_time

    elif language == "ruby":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".rb") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        run_proc = subprocess.run(
            ["ruby", tmpfile_path],
            input=program_input,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        exec_time = time.time() - start_time
        if run_proc.returncode == 0:
            return "✅ Ruby code is syntactically correct.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "php":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".php") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        run_proc = subprocess.run(
            ["php", tmpfile_path],
            input=program_input,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        exec_time = time.time() - start_time
        if run_proc.returncode == 0:
            return "✅ PHP code is syntactically correct.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "go":
        with tempfile.TemporaryDirectory() as tmpdir:
            go_file = os.path.join(tmpdir, "main.go")
            with open(go_file, "w") as f:
                f.write(code)

            run_proc = subprocess.run(
                ["go", "run", go_file],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            if run_proc.returncode == 0:
                return "✅ Go code is syntactically correct.", run_proc.stdout, exec_time
            else:
                return run_proc.stderr, None, exec_time

    elif language == "rust":
        with tempfile.TemporaryDirectory() as tmpdir:
            rust_file = os.path.join(tmpdir, "main.rs")
            exe_file = os.path.join(tmpdir, "main.exe")
            with open(rust_file, "w") as f:
                f.write(code)

            compile_proc = subprocess.run(
                ["rustc", rust_file, "-o", exe_file],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            if compile_proc.returncode != 0:
                exec_time = time.time() - start_time
                return compile_proc.stderr, None, exec_time

            run_proc = subprocess.run(
                [exe_file],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            return "✅ Rust code is syntactically correct.", run_proc.stdout if run_proc.stdout else run_proc.stderr, exec_time

    elif language == "bash":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".sh", mode='w') as tmpfile:
            tmpfile.write(code)
            tmpfile_path = tmpfile.name

        run_proc = subprocess.run(
            ["bash", tmpfile_path],
            input=program_input,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        exec_time = time.time() - start_time
        if run_proc.returncode == 0:
            return "✅ Bash script executed successfully.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "perl":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".pl") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        run_proc = subprocess.run(
            ["perl", tmpfile_path],
            input=program_input,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        exec_time = time.time() - start_time
        if run_proc.returncode == 0:
            return "✅ Perl code executed successfully.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "r":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".R") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        run_proc = subprocess.run(
            ["Rscript", tmpfile_path],
            input=program_input,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        exec_time = time.time() - start_time
        if run_proc.returncode == 0:
            return "✅ R code executed successfully.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "kotlin":
        with tempfile.TemporaryDirectory() as tmpdir:
            kt_file = os.path.join(tmpdir, "Main.kt")
            with open(kt_file, "w") as f:
                f.write(code)

            compile_proc = subprocess.run(
                ["kotlinc", kt_file, "-include-runtime", "-d", os.path.join(tmpdir, "Main.jar")],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            if compile_proc.returncode != 0:
                exec_time = time.time() - start_time
                return compile_proc.stderr, None, exec_time

            run_proc = subprocess.run(
                ["java", "-jar", os.path.join(tmpdir, "Main.jar")],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            return "✅ Kotlin code executed successfully.", run_proc.stdout if run_proc.stdout else run_proc.stderr, exec_time

    elif language == "swift":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".swift") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        run_proc = subprocess.run(
            ["swift", tmpfile_path],
            input=program_input,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        exec_time = time.time() - start_time
        if run_proc.returncode == 0:
            return "✅ Swift code executed successfully.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "scala":
        with tempfile.TemporaryDirectory() as tmpdir:
            scala_file = os.path.join(tmpdir, "Main.scala")
            with open(scala_file, "w") as f:
                f.write(code)

            run_proc = subprocess.run(
                ["scala", scala_file],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            if run_proc.returncode == 0:
                return "✅ Scala code executed successfully.", run_proc.stdout, exec_time
            else:
                return run_proc.stderr, None, exec_time

    elif language == "lua":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".lua") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        run_proc = subprocess.run(
            ["lua", tmpfile_path],
            input=program_input,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        exec_time = time.time() - start_time
        if run_proc.returncode == 0:
            return "✅ Lua code executed successfully.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "typescript":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".ts") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        # Compile TypeScript to JavaScript
        compile_proc = subprocess.run(
            ["tsc", tmpfile_path, "--outDir", os.path.dirname(tmpfile_path)],
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        
        js_file = tmpfile_path.replace('.ts', '.js')
        if os.path.exists(js_file):
            run_proc = subprocess.run(
                ["node", js_file],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            return "✅ TypeScript code executed successfully.", run_proc.stdout, exec_time
        else:
            exec_time = time.time() - start_time
            return compile_proc.stderr, None, exec_time

    elif language == "dart":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".dart") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        run_proc = subprocess.run(
            ["dart", tmpfile_path],
            input=program_input,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        exec_time = time.time() - start_time
        if run_proc.returncode == 0:
            return "✅ Dart code executed successfully.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "fortran":
        with tempfile.TemporaryDirectory() as tmpdir:
            f_file = os.path.join(tmpdir, "program.f90")
            exe_file = os.path.join(tmpdir, "program.exe")
            with open(f_file, "w") as f:
                f.write(code)

            compile_proc = subprocess.run(
                ["gfortran", f_file, "-o", exe_file],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            if compile_proc.returncode != 0:
                exec_time = time.time() - start_time
                return compile_proc.stderr, None, exec_time

            run_proc = subprocess.run(
                [exe_file],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            return "✅ Fortran code executed successfully.", run_proc.stdout if run_proc.stdout else run_proc.stderr, exec_time

    elif language == "cobol":
        with tempfile.TemporaryDirectory() as tmpdir:
            cob_file = os.path.join(tmpdir, "program.cob")
            exe_file = os.path.join(tmpdir, "program.exe")
            with open(cob_file, "w") as f:
                f.write(code)

            compile_proc = subprocess.run(
                ["cobc", "-x", "-o", exe_file, cob_file],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            if compile_proc.returncode != 0:
                exec_time = time.time() - start_time
                return compile_proc.stderr, None, exec_time

            run_proc = subprocess.run(
                [exe_file],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            return "✅ Cobol code executed successfully.", run_proc.stdout if run_proc.stdout else run_proc.stderr, exec_time

    elif language == "pascal":
        with tempfile.TemporaryDirectory() as tmpdir:
            pas_file = os.path.join(tmpdir, "program.pas")
            exe_file = os.path.join(tmpdir, "program.exe")
            with open(pas_file, "w") as f:
                f.write(code)

            compile_proc = subprocess.run(
                ["fpc", pas_file, "-o" + exe_file],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            if compile_proc.returncode != 0:
                exec_time = time.time() - start_time
                return compile_proc.stderr, None, exec_time

            run_proc = subprocess.run(
                [exe_file],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            return "✅ Pascal code executed successfully.", run_proc.stdout if run_proc.stdout else run_proc.stderr, exec_time

    elif language == "haskell":
        with tempfile.TemporaryDirectory() as tmpdir:
            hs_file = os.path.join(tmpdir, "Main.hs")
            with open(hs_file, "w") as f:
                f.write(code)

            run_proc = subprocess.run(
                ["runhaskell", hs_file],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            if run_proc.returncode == 0:
                return "✅ Haskell code executed successfully.", run_proc.stdout, exec_time
            else:
                return run_proc.stderr, None, exec_time

    elif language == "objectivec":
        with tempfile.TemporaryDirectory() as tmpdir:
            m_file = os.path.join(tmpdir, "program.m")
            exe_file = os.path.join(tmpdir, "program.exe")
            with open(m_file, "w") as f:
                f.write(code)

            compile_proc = subprocess.run(
                ["gcc", m_file, "-o", exe_file, "-lobjc", "-framework", "Foundation"],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            if compile_proc.returncode != 0:
                exec_time = time.time() - start_time
                return compile_proc.stderr, None, exec_time

            run_proc = subprocess.run(
                [exe_file],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            return "✅ Objective-C code executed successfully.", run_proc.stdout if run_proc.stdout else run_proc.stderr, exec_time

    elif language == "assembly":
        with tempfile.TemporaryDirectory() as tmpdir:
            asm_file = os.path.join(tmpdir, "program.asm")
            obj_file = os.path.join(tmpdir, "program.o")
            exe_file = os.path.join(tmpdir, "program.exe")
            with open(asm_file, "w") as f:
                f.write(code)

            compile_proc = subprocess.run(
                ["nasm", "-f", "elf64", asm_file, "-o", obj_file],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            if compile_proc.returncode != 0:
                exec_time = time.time() - start_time
                return compile_proc.stderr, None, exec_time

            link_proc = subprocess.run(
                ["ld", obj_file, "-o", exe_file],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            if link_proc.returncode != 0:
                exec_time = time.time() - start_time
                return link_proc.stderr, None, exec_time

            run_proc = subprocess.run(
                [exe_file],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            return "✅ Assembly code executed successfully.", run_proc.stdout if run_proc.stdout else run_proc.stderr, exec_time

    elif language == "prolog":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".pl") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        run_proc = subprocess.run(
            ["swipl", "-q", "-t", "halt", "-s", tmpfile_path],
            input=program_input,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        exec_time = time.time() - start_time
        if run_proc.returncode == 0:
            return "✅ Prolog code executed successfully.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "lisp":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".lisp") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        run_proc = subprocess.run(
            ["sbcl", "--script", tmpfile_path],
            input=program_input,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        exec_time = time.time() - start_time
        if run_proc.returncode == 0:
            return "✅ Common Lisp code executed successfully.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "scheme":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".scm") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        # Try Guile first, fallback to Racket
        try:
            run_proc = subprocess.run(
                ["guile", tmpfile_path],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
        except FileNotFoundError:
            # Fallback to Racket if Guile not found
            run_proc = subprocess.run(
                ["racket", tmpfile_path],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
        
        exec_time = time.time() - start_time
        os.unlink(tmpfile_path)  # Clean up temp file
        if run_proc.returncode == 0:
            return "✅ Scheme code executed successfully.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "erlang":
        with tempfile.TemporaryDirectory() as tmpdir:
            erl_file = os.path.join(tmpdir, "hello.erl")
            with open(erl_file, "w") as f:
                f.write(code)

            run_proc = subprocess.run(
                ["escript", erl_file],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            if run_proc.returncode == 0:
                return "✅ Erlang code executed successfully.", run_proc.stdout, exec_time
            else:
                return run_proc.stderr, None, exec_time

    elif language == "elixir":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".exs") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        run_proc = subprocess.run(
            ["elixir", tmpfile_path],
            input=program_input,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        exec_time = time.time() - start_time
        if run_proc.returncode == 0:
            return "✅ Elixir code executed successfully.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "clojure":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".clj") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        run_proc = subprocess.run(
            ["clojure", tmpfile_path],
            input=program_input,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        exec_time = time.time() - start_time
        if run_proc.returncode == 0:
            return "✅ Clojure code executed successfully.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "fsharp":
        with tempfile.NamedTemporaryFile(delete=False, suffix=".fsx") as tmpfile:
            tmpfile.write(code.encode("utf-8"))
            tmpfile_path = tmpfile.name

        run_proc = subprocess.run(
            ["dotnet", "fsi", tmpfile_path],
            input=program_input,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
        )
        exec_time = time.time() - start_time
        if run_proc.returncode == 0:
            return "✅ F# code executed successfully.", run_proc.stdout, exec_time
        else:
            return run_proc.stderr, None, exec_time

    elif language == "vb":
        with tempfile.TemporaryDirectory() as tmpdir:
            vb_file = os.path.join(tmpdir, "Program.vb")
            exe_file = os.path.join(tmpdir, "Program.exe")
            with open(vb_file, "w") as f:
                f.write(code)

            compile_proc = subprocess.run(
                ["vbnc", vb_file, "-out:" + exe_file],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            if compile_proc.returncode != 0:
                exec_time = time.time() - start_time
                return compile_proc.stderr, None, exec_time

            run_proc = subprocess.run(
                ["mono", exe_file],
                input=program_input,
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True
            )
            exec_time = time.time() - start_time
            return "✅ Visual Basic code executed successfully.", run_proc.stdout if run_proc.stdout else run_proc.stderr, exec_time

    exec_time = time.time() - start_time
    return "❌ Language not supported", None, exec_time


@app.route("/", methods=["GET", "POST"])
def index():
    result = ""
    output = ""
    code = ""
    program_input = ""
    language = "java"
    exec_time = 0
    
    # Initialize history in session
    if 'history' not in session:
        session['history'] = []
    
    if request.method == "POST":
        language = request.form["language"]
        code = request.form["code"]
        program_input = request.form["program_input"]
        result, output_text, exec_time = check_and_run_code(language, code, program_input)
        
        final_output = output_text if output_text else result
        
        # Save to history
        history_entry = {
            'language': language,
            'code': code[:100] + '...' if len(code) > 100 else code,
            'timestamp': datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
            'success': '✅' in result
        }
        session['history'].insert(0, history_entry)
        session['history'] = session['history'][:10]  # Keep last 10
        session.modified = True
        
        if request.form.get('ajax') == 'true':
            return jsonify({'output': final_output, 'result': result})

    return render_template(
        "index.html",
        result=result,
        output=output,
        code=code,
        program_input=program_input,
        language=language,
        exec_time=exec_time,
        history=session.get('history', []),
        templates=CODE_TEMPLATES
    )

@app.route("/clear_history", methods=["POST"])
def clear_history():
    session['history'] = []
    return jsonify({'success': True})





@app.route("/download_code", methods=["POST"])
def download_code():
    """Download code as file"""
    data = request.json
    code = data.get('code', '')
    language = data.get('language', 'python')
    
    # File extensions mapping
    extensions = {
        'python': 'py', 'java': 'java', 'javascript': 'js', 'c': 'c',
        'cpp': 'cpp', 'csharp': 'cs', 'ruby': 'rb', 'php': 'php',
        'go': 'go', 'rust': 'rs', 'bash': 'sh', 'perl': 'pl',
        'r': 'R', 'kotlin': 'kt', 'swift': 'swift', 'scala': 'scala',
        'lua': 'lua', 'typescript': 'ts', 'dart': 'dart'
    }
    
    ext = extensions.get(language, 'txt')
    filename = f"code.{ext}"
    
    # Create file in memory
    file_data = io.BytesIO(code.encode('utf-8'))
    file_data.seek(0)
    
    return send_file(
        file_data,
        mimetype='text/plain',
        as_attachment=True,
        download_name=filename
    )


@app.route("/upload_code", methods=["POST"])
def upload_code():
    """Upload code file"""
    if 'file' not in request.files:
        return jsonify({'success': False, 'error': 'No file provided'})
    
    file = request.files['file']
    if file.filename == '':
        return jsonify({'success': False, 'error': 'No file selected'})
    
    try:
        code = file.read().decode('utf-8')
        return jsonify({'success': True, 'code': code})
    except Exception as e:
        return jsonify({'success': False, 'error': str(e)})





@app.route("/save_snippet", methods=["POST"])
def save_snippet():
    """Save code snippet to library with unique ID"""
    data = request.json
    snippet_name = data.get('name', 'Untitled')
    code = data.get('code', '')
    language = data.get('language', 'python')
    folder = data.get('folder', 'root')
    
    if 'snippets' not in session:
        session['snippets'] = []
    
    snippet_id = str(uuid.uuid4())[:8]
    snippet = {
        'id': snippet_id,
        'name': snippet_name,
        'code': code,
        'language': language,
        'folder': folder,
        'is_deleted': False,
        'timestamp': datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }
    
    session['snippets'].insert(0, snippet)
    if len(session['snippets']) > 100:
        session['snippets'] = session['snippets'][:100]
    session.modified = True
    
    return jsonify({'success': True, 'id': snippet_id})


@app.route("/get_snippets", methods=["GET"])
def get_snippets():
    """Get saved snippets"""
    return jsonify({'snippets': session.get('snippets', [])})


@app.route("/delete_snippet", methods=["POST"])
def delete_snippet():
    """Soft delete a snippet by ID"""
    data = request.json
    sid = data.get('id')
    
    if 'snippets' in session:
        for s in session['snippets']:
            if s.get('id') == sid:
                s['is_deleted'] = True
                session.modified = True
                return jsonify({'success': True})
    
    return jsonify({'success': False, 'error': 'Project not found'})


@app.route("/restore_snippet", methods=["POST"])
def restore_snippet():
    """Restore snippet from bin by ID"""
    data = request.json
    sid = data.get('id')
    
    if 'snippets' in session:
        for s in session['snippets']:
            if s.get('id') == sid:
                s['is_deleted'] = False
                session.modified = True
                return jsonify({'success': True})
    
    return jsonify({'success': False})


@app.route("/permanent_delete", methods=["POST"])
def permanent_delete():
    """Permanently delete snippet by ID"""
    data = request.json
    sid = data.get('id')
    
    if 'snippets' in session:
        session['snippets'] = [s for s in session['snippets'] if s.get('id') != sid]
        session.modified = True
        return jsonify({'success': True})
    
    return jsonify({'success': False})


@app.route("/batch_update_folder", methods=["POST"])
def batch_update_folder():
    """Move all active snippets to a specific folder"""
    data = request.json
    folder_name = data.get('folder', 'root')
    
    if 'snippets' in session:
        for s in session['snippets']:
            if not s.get('is_deleted', False):
                s['folder'] = folder_name
        session.modified = True
        return jsonify({'success': True})
    
    return jsonify({'success': False})


@app.route("/analyze_code", methods=["POST"])
def analyze_code():
    """Analyze code complexity and provide statistics"""
    data = request.json
    code = data.get('code', '')
    language = data.get('language', 'python')
    
    stats = {
        'lines': len(code.split('\n')),
        'characters': len(code),
        'words': len(code.split()),
        'non_empty_lines': len([line for line in code.split('\n') if line.strip()])
    }
    
    # Language-specific analysis
    if language == 'python':
        stats['functions'] = code.count('def ')
        stats['classes'] = code.count('class ')
        stats['imports'] = code.count('import ')
    elif language in ['java', 'c', 'cpp', 'csharp']:
        stats['functions'] = code.count('void ') + code.count('int ') + code.count('public ')
        stats['classes'] = code.count('class ')
    
    return jsonify({'success': True, 'stats': stats})


@app.route("/export_project", methods=["POST"])
def export_project():
    """Export entire project as ZIP"""
    data = request.json
    files = data.get('files', [])
    
    # Create ZIP in memory
    zip_buffer = io.BytesIO()
    
    with zipfile.ZipFile(zip_buffer, 'w', zipfile.ZIP_DEFLATED) as zip_file:
        for file_data in files:
            filename = file_data.get('name', 'file.txt')
            content = file_data.get('content', '')
            zip_file.writestr(filename, content)
    
    zip_buffer.seek(0)
    
    return send_file(
        zip_buffer,
        mimetype='application/zip',
        as_attachment=True,
        download_name='project.zip'
    )


@app.route("/format_code", methods=["POST"])
def format_code():
    """Simple code beautifier/indenter"""
    data = request.json
    code = data.get('code', '')
    language = data.get('language', 'python')
    
    if not code:
        return jsonify({'success': False, 'error': 'No code provided'})
        
    lines = code.split('\n')
    formatted_lines = []
    indent_level = 0
    indent_size = 4
    
    for line in lines:
        stripped = line.strip()
        if not stripped:
            formatted_lines.append('')
            continue
            
        # Decrease indent if line starts with closing brace
        if stripped.startswith('}') or stripped.startswith(']') or stripped.startswith(')'):
            indent_level = max(0, indent_level - 1)
            
        formatted_lines.append(' ' * (indent_level * indent_size) + stripped)
        
        # Increase indent if line ends with opening brace or colon (Python/C)
        if stripped.endswith('{') or stripped.endswith('[') or stripped.endswith('(') or (language == 'python' and stripped.endswith(':')):
            indent_level += 1
            
    return jsonify({'success': True, 'code': '\n'.join(formatted_lines)})


@app.route("/share_code", methods=["POST"])
def share_code():
    """Share code by saving it to a public folder"""
    data = request.json
    code = data.get('code', '')
    language = data.get('language', 'python')
    
    if not code:
        return jsonify({'success': False, 'error': 'No code provided'})
        
    share_id = hashlib.md5(code.encode()).hexdigest()[:10]
    share_path = os.path.join('shared_codes', f"{share_id}.json")
    
    share_data = {
        'code': code,
        'language': language,
        'timestamp': datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }
    
    with open(share_path, 'w') as f:
        json.dump(share_data, f)
        
    share_url = url_for('view_shared', share_id=share_id, _external=True)
    return jsonify({'success': True, 'url': share_url})


@app.route("/view_shared/<share_id>")
def view_shared(share_id):
    """View shared code page"""
    share_path = os.path.join('shared_codes', f"{share_id}.json")
    if not os.path.exists(share_path):
        return "Shared code not found", 404
        
    with open(share_path, 'r') as f:
        share_data = json.load(f)
        
    return render_template(
        "index.html",
        code=share_data['code'],
        language=share_data['language'],
        output="--- Shared Project ---\nLoaded from shared link.",
        history=session.get('history', []),
        templates=CODE_TEMPLATES
    )


if __name__ == "__main__":
    app.run(debug=True, port=2006)
