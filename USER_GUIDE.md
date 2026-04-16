# 👤 User Guide: Complete Walkthrough

Welcome to the Online IDE! This guide will walk you through all features step by step.

---

## 🚀 Getting Started

### First Time Setup

1. **Install Dependencies**
   ```bash
   pip install -r requirements.txt
   ```

2. **Start the Application**
   ```bash
   python app.py
   ```

3. **Open in Browser**
   ```
   http://localhost:5000
   ```

---

## 📝 Basic Usage

### Writing Your First Program

1. **Select Language**
   - Click the language dropdown
   - Choose your preferred language (e.g., Python)

2. **Write Code**
   - Click in the editor area
   - Type or paste your code
   - Use Tab for indentation

3. **Add Input (Optional)**
   - Click in the "Input (stdin)" section
   - Type input values (one per line)

4. **Run Code**
   - Click the **▶ Run** button
   - Wait for execution
   - View output in the "Output" section

### Example: Hello World in Python
```python
# 1. Select "Python" from dropdown
# 2. Type this code:
print("Hello, World!")

# 3. Click ▶ Run
# 4. See output: Hello, World!
```

---

## 🎨 Using Templates

### Load a Template

1. Click **📄 New** button
2. Template loads for selected language
3. Modify as needed
4. Click **▶ Run**

### Available Templates
Every language has a "Hello World" template ready to use!

---

## 🔗 Sharing Your Code

### Create a Share Link

1. **Write Your Code**
   ```python
   def greet(name):
       return f"Hello, {name}!"
   
   print(greet("World"))
   ```

2. **Click 🔗 Share Button**
   - Link generates automatically
   - Copies to clipboard
   - Shows alert with URL

3. **Share the Link**
   ```
   http://localhost:5000/shared/abc123def4
   ```

4. **Others Can View**
   - They open the link
   - See your code
   - Can run it themselves

### What Gets Shared?
- ✅ Your code
- ✅ Selected language
- ✅ Input data (if any)
- ❌ Output (they run it themselves)

---

## 📁 File Management

### Upload a Code File

1. **Click 📁 Upload Button**
2. **Select File**
   - Choose from your computer
   - Supported: `.py`, `.java`, `.js`, `.c`, `.cpp`, etc.
3. **Code Loads Automatically**
   - Appears in editor
   - Ready to run

### Download Your Code

1. **Write Your Code**
2. **Click 💾 Download Button**
3. **File Saves Automatically**
   - Correct extension (e.g., `code.py`)
   - Downloads to your computer

### Supported File Types
```
Python:     .py
Java:       .java
JavaScript: .js
C:          .c
C++:        .cpp
C#:         .cs
Ruby:       .rb
PHP:        .php
Go:         .go
Rust:       .rs
TypeScript: .ts
And more...
```

---

## 📚 Code Snippets Library

### Save a Snippet

1. **Write Code You Want to Save**
   ```python
   def fibonacci(n):
       if n <= 1:
           return n
       return fibonacci(n-1) + fibonacci(n-2)
   ```

2. **Click 📚 Snippets Button**
   - Sidebar opens

3. **Click 💾 Save Current**
   - Enter a name (e.g., "Fibonacci Function")
   - Click OK

4. **Snippet Saved!**
   - Appears in sidebar
   - Stored in session

### Load a Snippet

1. **Click 📚 Snippets**
2. **Click on Snippet Name**
3. **Code Loads into Editor**
4. **Modify and Run**

### Delete a Snippet

1. **Open Snippets Sidebar**
2. **Find Snippet to Delete**
3. **Click 🗑️ Button**
4. **Confirm Deletion**

### Snippet Management
- Stores last 20 snippets
- Organized by name and language
- Shows timestamp
- Preview of code

---

## 📊 Code Analysis

### Analyze Your Code

1. **Write Your Code**
   ```python
   import math
   
   def calculate_area(radius):
       return math.pi * radius ** 2
   
   class Circle:
       def __init__(self, radius):
           self.radius = radius
   ```

2. **Click 📊 Analyze Button**

3. **View Statistics**
   ```
   📊 Code Analysis:
   
   Total Lines: 8
   Non-empty Lines: 6
   Characters: 156
   Words: 23
   Functions: 1
   Classes: 1
   Imports: 1
   ```

### What Gets Analyzed?
- **All Languages**:
  - Total lines
  - Non-empty lines
  - Character count
  - Word count

- **Python**:
  - Function count (`def`)
  - Class count (`class`)
  - Import count (`import`)

- **Java/C/C++/C#**:
  - Function count
  - Class count

---

## 🕒 Execution History

### View History

1. **Click 🕒 History Button**
2. **Sidebar Opens**
3. **See Last 10 Executions**

### History Information
Each entry shows:
- Language used
- Success/failure indicator (✅/❌)
- Timestamp
- Code preview (first 100 characters)

### Clear History

1. **Click History Button**
2. **Scroll to bottom**
3. **Click "Clear History"**
4. **Confirm**

---

## 🌙 Theme Switching

### Toggle Theme

1. **Click 🌙 Theme Button**
2. **Theme Switches Instantly**
   - Light → Dark
   - Dark → Light

### Theme Features
- **Light Mode (Eclipse)**:
  - White background
  - Dark text
  - Good for daytime

- **Dark Mode (Monokai)**:
  - Dark background
  - Colored syntax
  - Good for nighttime

### Theme Persistence
Your theme choice is saved automatically!

---

## 💾 Output Management

### Download Output

1. **Run Your Code**
2. **View Output**
3. **Click "Save Output" (in toolbar)**
4. **File Downloads**
   - Format: `output_python_2025-11-29.txt`
   - Contains execution results

---

## 🎯 Advanced Features

### Multi-Language Workflow

**Example: Compare Python and JavaScript**

1. **Write Python Version**
   ```python
   # Select Python
   numbers = [1, 2, 3, 4, 5]
   squared = [x**2 for x in numbers]
   print(squared)
   ```

2. **Save as Snippet**
   - Name: "Python Squares"

3. **Switch to JavaScript**
   ```javascript
   // Select JavaScript
   const numbers = [1, 2, 3, 4, 5];
   const squared = numbers.map(x => x**2);
   console.log(squared);
   ```

4. **Save as Snippet**
   - Name: "JavaScript Squares"

5. **Compare Results**
   - Load each snippet
   - Run and compare

---

## 🔧 Tips & Tricks

### Productivity Tips

1. **Use Keyboard Shortcuts**
   - Tab: Indent
   - Shift+Tab: Unindent
   - Ctrl/Cmd+A: Select all
   - Ctrl/Cmd+C/V: Copy/Paste

2. **Build Snippet Library**
   - Save common patterns
   - Quick access to utilities
   - Share with team

3. **Analyze Before Running**
   - Check code complexity
   - Verify structure
   - Catch issues early

4. **Use Templates**
   - Start with working code
   - Modify incrementally
   - Learn syntax quickly

5. **Share for Review**
   - Generate share link
   - Get feedback
   - Collaborate easily

### Code Organization

1. **Name Snippets Clearly**
   - "Sort Algorithm - Bubble"
   - "API Request - GET"
   - "Database Connection"

2. **Use Comments**
   ```python
   # Purpose: Calculate factorial
   # Input: Integer n
   # Output: n!
   def factorial(n):
       # Implementation
   ```

3. **Test Incrementally**
   - Write small pieces
   - Test each part
   - Build up complexity

---

## 🐛 Troubleshooting

### Common Issues

#### Code Won't Run
**Problem**: Click Run, nothing happens  
**Solution**:
1. Check language selection
2. Verify syntax
3. Look for error messages
4. Try template first

#### Upload Failed
**Problem**: File won't upload  
**Solution**:
1. Check file size (max 16MB)
2. Verify file extension
3. Try different browser
4. Check file permissions

#### Share Link Not Working
**Problem**: Link doesn't load code  
**Solution**:
1. Check internet connection
2. Verify link copied correctly
3. Try generating new link
4. Check server is running

#### Output Not Showing
**Problem**: Code runs but no output  
**Solution**:
1. Add print statements
2. Check for errors
3. Verify input is correct
4. Look in error section

---

## 📖 Language-Specific Guides

### Python
```python
# Input/Output
name = input("Enter name: ")
print(f"Hello, {name}!")

# Lists
numbers = [1, 2, 3, 4, 5]
print(sum(numbers))

# Functions
def greet(name):
    return f"Hello, {name}!"
```

### Java
```java
// Input/Output
import java.util.Scanner;

Scanner sc = new Scanner(System.in);
String name = sc.nextLine();
System.out.println("Hello, " + name + "!");

// Arrays
int[] numbers = {1, 2, 3, 4, 5};
System.out.println(Arrays.toString(numbers));
```

### JavaScript
```javascript
// Input/Output (Node.js)
const readline = require('readline');
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

rl.question('Enter name: ', (name) => {
    console.log(`Hello, ${name}!`);
    rl.close();
});

// Arrays
const numbers = [1, 2, 3, 4, 5];
console.log(numbers);
```

### C
```c
// Input/Output
#include <stdio.h>

int main() {
    char name[50];
    printf("Enter name: ");
    scanf("%s", name);
    printf("Hello, %s!\n", name);
    return 0;
}
```

### C++
```cpp
// Input/Output
#include <iostream>
#include <string>
using namespace std;

int main() {
    string name;
    cout << "Enter name: ";
    cin >> name;
    cout << "Hello, " << name << "!" << endl;
    return 0;
}
```

---

## 🎓 Learning Path

### Beginner
1. Start with Python (easiest syntax)
2. Use templates
3. Modify examples
4. Save useful snippets
5. Practice daily

### Intermediate
1. Try multiple languages
2. Compare implementations
3. Build snippet library
4. Share code for review
5. Analyze code complexity

### Advanced
1. Optimize algorithms
2. Compare performance
3. Build reusable utilities
4. Contribute snippets
5. Help others learn

---

## 📊 Feature Summary

### Quick Reference Table

| Feature | Button | What It Does |
|---------|--------|--------------|
| Run Code | ▶ | Execute your code |
| New Template | 📄 | Load language template |
| Share Code | 🔗 | Generate share link |
| Download | 💾 | Save code to file |
| Upload | 📁 | Import code file |
| Snippets | 📚 | Manage code library |
| Analyze | 📊 | Show statistics |
| History | 🕒 | View past runs |
| Theme | 🌙 | Toggle dark/light |

---

## 🎯 Best Practices

### Code Quality
1. ✅ Use meaningful variable names
2. ✅ Add comments for complex logic
3. ✅ Test with different inputs
4. ✅ Handle errors gracefully
5. ✅ Keep functions small and focused

### Workflow
1. ✅ Start with template
2. ✅ Write small pieces
3. ✅ Test frequently
4. ✅ Save working versions
5. ✅ Share for feedback

### Organization
1. ✅ Name snippets clearly
2. ✅ Group related code
3. ✅ Document complex algorithms
4. ✅ Clean up old snippets
5. ✅ Back up important code

---

## 🆘 Getting Help

### Resources
- **FEATURES.md** - Complete feature list
- **QUICK_REFERENCE.md** - Quick reference
- **API_DOCUMENTATION.md** - API details
- **COMPARISON.md** - vs Online GDB

### Support
- Check documentation first
- Try examples provided
- Test with simple code
- Review error messages

---

## 🎉 Conclusion

You now know how to use all features of the Online IDE!

### Key Takeaways
- ✅ Write and run code in 33+ languages
- ✅ Share code with unique links
- ✅ Manage files (upload/download)
- ✅ Build snippet library
- ✅ Analyze code statistics
- ✅ Track execution history
- ✅ Customize with themes

### Next Steps
1. Try writing your first program
2. Save it as a snippet
3. Share with a friend
4. Explore different languages
5. Build your code library

---

**Happy Coding! 🚀**

**Version**: 2.0.0  
**Last Updated**: November 29, 2025  
**Status**: Complete Guide
