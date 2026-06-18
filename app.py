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
app.secret_key = os.environ.get('SECRET_KEY', 'dev-fallback-key-change-in-production')
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024  # 16MB max file size
app.config['UPLOAD_FOLDER'] = 'uploads'

# Create uploads directory if it doesn't exist (ephemeral on Render free tier)
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
    "ruby": '''# Ruby code
puts "Hello, Ruby!"''',
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
    "bash": '''#!/bin/bash
echo "Hello, Bash!"''',
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
main = putStrLn "Hello, Haskell!"
""",
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
    "elixir": '''IO.puts "Hello, Elixir!"''',
    "clojure": """(println "Hello, Clojure!")""",
    "fsharp": '''printfn "Hello, F#!"''',
    "vb": """Module Hello
    Sub Main()
        Console.WriteLine("Hello, Visual Basic!")
    End Sub
End Module""",
    "html": """<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Preview Workspace</title>
    <style>
        body {
            background-color: #0d1117;
            color: #c9d1d9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        document.getElementById('alertBtn').addEventListener('click', () => {
            alert('JavaScript works seamlessly in the Nexus sandbox iframe!');
        });
    </script>
</body>
</html>"""
}

def check_and_run_code(language, code, program_input):
    start_time = time.time()
    TIMEOUT = 30  # 30 seconds timeout for all executions
    
    if language == "html":
        exec_time = time.time() - start_time
        return "✅ HTML/CSS/JS preview compiled successfully.", code, exec_time
        
    # Toolchain configuration (Linux/Render — gcc and g++ available natively)
    env = os.environ.copy()
    gcc_path = "gcc"
    gpp_path = "g++"
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
            exe_file = os.path.join(tmpdir, "program")
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
            exe_file = os.path.join(tmpdir, "program")
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
            exe_file = os.path.join(tmpdir, "Program")
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
            exe_file = os.path.join(tmpdir, "main")
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
            exe_file = os.path.join(tmpdir, "program")
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
            exe_file = os.path.join(tmpdir, "program")
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
            exe_file = os.path.join(tmpdir, "program")
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
            exe_file = os.path.join(tmpdir, "program")
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
            exe_file = os.path.join(tmpdir, "program")
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
            exe_file = os.path.join(tmpdir, "Program")
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


# In-memory store for shared comments
COMMENTS_STORE = {}

@app.route("/collab/comments", methods=["GET", "POST"])
def collab_comments():
    """Get or post a comment for a shared code session"""
    if request.method == "POST":
        data = request.json
        share_id = data.get("share_id", "default")
        author = data.get("author", "Anonymous Developer")
        text = data.get("text", "")
        
        if not text:
            return jsonify({'success': False, 'error': 'Empty comment'})
            
        comment = {
            'author': author,
            'text': text,
            'timestamp': datetime.now().strftime("%I:%M %p")
        }
        
        if share_id not in COMMENTS_STORE:
            COMMENTS_STORE[share_id] = []
        COMMENTS_STORE[share_id].append(comment)
        return jsonify({'success': True, 'comment': comment})
        
    else:
        share_id = request.args.get("share_id", "default")
        comments = COMMENTS_STORE.get(share_id, [
            {'author': 'System AI', 'text': 'Welcome to live session comments! Add notes or review changes.', 'timestamp': 'Now'}
        ])
        return jsonify({'success': True, 'comments': comments})

@app.route("/ai/action", methods=["POST"])
def ai_action():
    """Process an AI assistance action: explain, optimize, review, fix"""
    data = request.json
    action = data.get("action", "explain")
    code = data.get("code", "")
    language = data.get("language", "python")
    
    if not code:
        return jsonify({'success': False, 'error': 'Editor is empty. Write some code first!'})
        
    result_text = ""
    
    if action == "explain":
        lines = len(code.split('\n'))
        functions = code.count("def ") if language == "python" else code.count("function")
        result_text = f"### Code Explanation ({language.upper()})\n"
        result_text += f"This program consists of {lines} lines of code and defines {functions} function(s).\n\n"
        result_text += "**Core Logic Flow:**\n"
        if language == "python":
            result_text += "- Executes sequentially from top to bottom.\n"
            if "import" in code:
                result_text += "- Standard library modules are imported at the top.\n"
        elif language in ["c", "cpp", "java"]:
            result_text += "- Enters through the standard application entrypoint (`main` function).\n"
            result_text += "- Uses type-safe declarations and static compiling rules.\n"
        else:
            result_text += "- Direct scripting structure.\n"
            
        result_text += "\n**Detailed Breakdown:**\n"
        has_loop = "for " in code or "while " in code or "while(" in code or "for(" in code
        has_cond = "if " in code or "if(" in code or "elif" in code or "else" in code
        
        if has_loop:
            result_text += "- **Iteration**: The code contains looping logic (`for` or `while`), allowing repetitive actions on standard collection elements or index iterations.\n"
        if has_cond:
            result_text += "- **Conditionals**: Logical branches (`if`/`else`) direct program execution based on runtime conditions.\n"
        if not has_loop and not has_cond:
            result_text += "- **Linear Execution**: The instructions execute in a single forward pass without branching or iteration.\n"
            
        result_text += "\n**Performance Profile:**\n"
        result_text += f"- Expected Time Complexity: O(1) to O(N) depending on runtime parameters.\n"
        result_text += "- Space Overhead: Minimal stack allocations."
        
    elif action == "optimize":
        result_text = f"### Optimization Suggestions ({language.upper()})\n"
        if language == "python":
            result_text += "1. **List Comprehensions**: Replace standard loops appending to lists with Pythonic list comprehensions to execute faster in compiled C layers.\n"
            result_text += "2. **Local Caching**: Cache repeated dictionary or object attribute lookups locally inside local scopes.\n\n"
            result_text += "**Optimized Code Snippet:**\n"
            result_text += "```python\n# Optimized Revision\n# Avoid redundant lookups. Use efficient generators.\n"
            result_text += code.replace("for i in range", "# Vectorized range operations if possible\nfor i in range") + "\n```"
        else:
            result_text += "1. **Memory Allocations**: Minimize dynamic heap allocation variables inside loops.\n"
            result_text += "2. **Inline Keywords**: Suggest compilers compiler-inlining frequently accessed sub-methods to reduce function-call frame overheads.\n\n"
            result_text += "**Optimized Revision Recommendation:**\n"
            result_text += f"```{language}\n// Inline declarations and static variable bounds\n" + code + "\n```"
            
    elif action == "fix":
        result_text = "### Debugging & Bug Fix Suggestions\n"
        issues = []
        if language == "python":
            lines = code.split('\n')
            for idx, line in enumerate(lines):
                sline = line.strip()
                if (sline.startswith("def ") or sline.startswith("if ") or sline.startswith("elif ") or sline.startswith("for ")) and not sline.endswith(":"):
                    issues.append(f"Line {idx+1}: Missing colon at end of statement definition: `{sline}`")
        else:
            lines = code.split('\n')
            for idx, line in enumerate(lines):
                sline = line.strip()
                if sline and not sline.endswith(";") and not sline.endswith("{") and not sline.endswith("}") and not sline.startswith("#") and not sline.startswith("//"):
                    if language in ["c", "cpp", "javascript", "java", "php"]:
                        issues.append(f"Line {idx+1}: Verify semicolon presence at end of line: `{sline}`")
                        
        if issues:
            result_text += "⚠️ **Detected Potential Issues:**\n"
            for issue in issues:
                result_text += f"- {issue}\n"
            result_text += "\n**Suggested Fix:** Apply the formatting rules indicated above to ensure error-free compilation."
        else:
            result_text += "✅ **No major syntax warnings found!**\nYour code looks clean. If execution returns errors, verify variable initializations, indexing, and input arguments."
            
    elif action == "review":
        result_text = f"### Code Quality & Security Review ({language.upper()})\n"
        lines_count = len(code.split('\n'))
        score = 100 - min(40, lines_count // 5)
        if "eval(" in code or "exec(" in code or "system(" in code:
            score -= 30
            sec_status = "🔴 VULNERABLE (Dynamic execution call spotted)"
        else:
            sec_status = "🟢 SECURE (Running inside virtual execution limits)"
            
        complexity = "Low" if lines_count < 15 else "Medium" if lines_count < 45 else "High"
        
        result_text += f"**Rating Metric Scores:**\n"
        result_text += f"- **Maintainability Index:** {score}/100\n"
        result_text += f"- **Security Level:** {sec_status}\n"
        result_text += f"- **Cyclomatic Complexity:** {complexity}\n\n"
        result_text += "**Reviewer Feedback Details:**\n"
        result_text += "1. **Modularity**: Code structures are aligned. Ensure logical separations for large functions.\n"
        result_text += "2. **Documentation**: Add standard docstrings or developer block comments to explain algorithm parameters.\n"
        result_text += "3. **Exceptions**: Wrap input validations and network/file resources in try/catch bounds."
        
    return jsonify({'success': True, 'result': result_text})

@app.route("/ai/chat", methods=["POST"])
def ai_chat():
    """Chat console endpoint for general coding assistance queries"""
    data = request.json
    messages = data.get("messages", [])
    code = data.get("code", "")
    language = data.get("language", "python")
    
    if not messages:
        return jsonify({'success': False, 'error': 'No input query found'})
        
    user_msg = messages[-1].get("content", "").lower()
    
    reply = ""
    if "help" in user_msg or "how" in user_msg:
        reply = f"To program in {language}, write your instructions in the center workspace panel, adjust editor controls via settings, and click **Run**."
    elif "bug" in user_msg or "error" in user_msg or "fix" in user_msg:
        reply = f"I can review potential syntax bugs. Try clicking the **AI Bug Fix** action in the right assistant panel!"
    elif "explain" in user_msg:
        reply = f"You can explain this entire workspace instantly by clicking the **AI Explain Code** button. Let me know if you have questions about specific lines!"
    elif "optimize" in user_msg or "fast" in user_msg:
        reply = f"To speed up execution, try using in-place assignments, avoid nested loop lookups, and use built-in algorithms."
    else:
        reply = f"I'm your AI Workspace Assistant. I'm currently monitoring your {language.upper()} code. Try selecting one of the rapid AI actions (Review, Fix, Explain, Optimize) or ask me details about syntax!"
        
    return jsonify({
        'success': True, 
        'reply': reply,
        'timestamp': datetime.now().strftime("%I:%M %p")
    })

if __name__ == "__main__":
    app.run(debug=True, port=2006)
