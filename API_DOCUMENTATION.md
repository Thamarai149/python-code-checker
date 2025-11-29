# 🔌 API Documentation

Complete API reference for the Online IDE backend.

---

## Base URL

```
http://localhost:5000
```

---

## Endpoints

### 1. Main Page

#### `GET /`
Renders the main IDE interface.

**Response**: HTML page

---

#### `POST /`
Executes code and returns results.

**Request Body** (form-data):
```
language: string (required) - Programming language
code: string (required) - Source code to execute
program_input: string (optional) - Standard input
```

**Response**: HTML page with results

**Example**:
```javascript
fetch('/', {
    method: 'POST',
    body: new FormData(document.getElementById('codeForm'))
})
```

---

### 2. Code Sharing

#### `POST /share`
Generate a shareable link for code.

**Request Body** (JSON):
```json
{
    "code": "print('Hello World')",
    "language": "python",
    "input": "optional input"
}
```

**Response** (JSON):
```json
{
    "success": true,
    "url": "http://localhost:5000/shared/abc123def4",
    "code_id": "abc123def4"
}
```

**Example**:
```javascript
fetch('/share', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        code: codeEditor.getValue(),
        language: 'python',
        input: ''
    })
})
.then(res => res.json())
.then(data => console.log(data.url));
```

---

#### `GET /shared/<code_id>`
Load shared code by ID.

**Parameters**:
- `code_id` (string) - Unique code identifier

**Response**: HTML page with loaded code

**Example**:
```
http://localhost:5000/shared/abc123def4
```

---

### 3. File Management

#### `POST /download_code`
Download code as a file.

**Request Body** (JSON):
```json
{
    "code": "print('Hello')",
    "language": "python"
}
```

**Response**: File download (text/plain)

**Example**:
```javascript
fetch('/download_code', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        code: codeEditor.getValue(),
        language: 'python'
    })
})
.then(res => res.blob())
.then(blob => {
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'code.py';
    a.click();
});
```

---

#### `POST /upload_code`
Upload a code file.

**Request Body** (multipart/form-data):
```
file: File (required) - Code file to upload
```

**Response** (JSON):
```json
{
    "success": true,
    "code": "file contents here"
}
```

**Error Response**:
```json
{
    "success": false,
    "error": "error message"
}
```

**Example**:
```javascript
const formData = new FormData();
formData.append('file', fileInput.files[0]);

fetch('/upload_code', {
    method: 'POST',
    body: formData
})
.then(res => res.json())
.then(data => {
    if (data.success) {
        codeEditor.setValue(data.code);
    }
});
```

---

### 4. Code Snippets

#### `POST /save_snippet`
Save a code snippet.

**Request Body** (JSON):
```json
{
    "name": "Hello World",
    "code": "print('Hello')",
    "language": "python"
}
```

**Response** (JSON):
```json
{
    "success": true
}
```

**Example**:
```javascript
fetch('/save_snippet', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        name: 'My Snippet',
        code: codeEditor.getValue(),
        language: 'python'
    })
})
.then(res => res.json())
.then(data => console.log('Saved!'));
```

---

#### `GET /get_snippets`
Retrieve all saved snippets.

**Response** (JSON):
```json
{
    "snippets": [
        {
            "name": "Hello World",
            "code": "print('Hello')",
            "language": "python",
            "timestamp": "2025-11-29 10:30:00"
        }
    ]
}
```

**Example**:
```javascript
fetch('/get_snippets')
    .then(res => res.json())
    .then(data => {
        console.log(data.snippets);
    });
```

---

#### `POST /delete_snippet`
Delete a snippet by index.

**Request Body** (JSON):
```json
{
    "index": 0
}
```

**Response** (JSON):
```json
{
    "success": true
}
```

**Example**:
```javascript
fetch('/delete_snippet', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ index: 0 })
})
.then(res => res.json())
.then(data => console.log('Deleted!'));
```

---

### 5. Code Analysis

#### `POST /analyze_code`
Analyze code and return statistics.

**Request Body** (JSON):
```json
{
    "code": "def hello():\n    print('Hello')",
    "language": "python"
}
```

**Response** (JSON):
```json
{
    "success": true,
    "stats": {
        "lines": 2,
        "characters": 35,
        "words": 4,
        "non_empty_lines": 2,
        "functions": 1,
        "classes": 0,
        "imports": 0
    }
}
```

**Example**:
```javascript
fetch('/analyze_code', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        code: codeEditor.getValue(),
        language: 'python'
    })
})
.then(res => res.json())
.then(data => console.log(data.stats));
```

---

### 6. Code Formatting

#### `POST /format_code`
Format/beautify code (Python only).

**Request Body** (JSON):
```json
{
    "code": "def hello():print('Hello')",
    "language": "python"
}
```

**Response** (JSON):
```json
{
    "success": true,
    "code": "def hello():\n    print('Hello')"
}
```

**Error Response**:
```json
{
    "success": false,
    "error": "autopep8 not installed"
}
```

**Example**:
```javascript
fetch('/format_code', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        code: codeEditor.getValue(),
        language: 'python'
    })
})
.then(res => res.json())
.then(data => {
    if (data.success) {
        codeEditor.setValue(data.code);
    }
});
```

---

### 7. History Management

#### `POST /clear_history`
Clear execution history.

**Response** (JSON):
```json
{
    "success": true
}
```

**Example**:
```javascript
fetch('/clear_history', { method: 'POST' })
    .then(res => res.json())
    .then(data => console.log('History cleared!'));
```

---

### 8. Project Export

#### `POST /export_project`
Export multiple files as ZIP.

**Request Body** (JSON):
```json
{
    "files": [
        {
            "name": "main.py",
            "content": "print('Hello')"
        },
        {
            "name": "utils.py",
            "content": "def helper(): pass"
        }
    ]
}
```

**Response**: ZIP file download

**Example**:
```javascript
fetch('/export_project', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        files: [
            { name: 'main.py', content: 'print("Hello")' },
            { name: 'utils.py', content: 'def helper(): pass' }
        ]
    })
})
.then(res => res.blob())
.then(blob => {
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'project.zip';
    a.click();
});
```

---

## Data Models

### Code Execution Result
```python
{
    'result': str,      # Success/error message
    'output': str,      # Program output
    'exec_time': float  # Execution time in seconds
}
```

### History Entry
```python
{
    'language': str,    # Programming language
    'code': str,        # Code preview (first 100 chars)
    'timestamp': str,   # ISO format timestamp
    'success': bool     # Execution success status
}
```

### Snippet
```python
{
    'name': str,        # Snippet name
    'code': str,        # Full code
    'language': str,    # Programming language
    'timestamp': str    # ISO format timestamp
}
```

### Code Statistics
```python
{
    'lines': int,           # Total lines
    'characters': int,      # Total characters
    'words': int,           # Total words
    'non_empty_lines': int, # Non-empty lines
    'functions': int,       # Function count (optional)
    'classes': int,         # Class count (optional)
    'imports': int          # Import count (optional)
}
```

---

## Session Data

### Session Variables
```python
session['history']   # List of history entries (max 10)
session['snippets']  # List of snippets (max 20)
```

---

## Error Handling

### Standard Error Response
```json
{
    "success": false,
    "error": "Error message here"
}
```

### HTTP Status Codes
- `200` - Success
- `404` - Not Found (shared code)
- `500` - Server Error

---

## File Storage

### Shared Codes
```
shared_codes/
  ├── abc123def4.json
  ├── xyz789ghi0.json
  └── ...
```

**Format**:
```json
{
    "code": "source code",
    "language": "python",
    "input": "stdin input",
    "timestamp": "2025-11-29T10:30:00"
}
```

### Uploads
```
uploads/
  ├── temp files during upload
  └── (cleaned up after processing)
```

---

## Configuration

### Flask App Config
```python
app.secret_key = 'your-secret-key'
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024  # 16MB
app.config['UPLOAD_FOLDER'] = 'uploads'
```

### Execution Timeout
```python
TIMEOUT = 30  # seconds
```

---

## Language Support

### Supported Languages
```python
languages = [
    'java', 'python', 'javascript', 'c', 'cpp', 'csharp',
    'ruby', 'php', 'go', 'rust', 'bash', 'perl', 'r',
    'kotlin', 'swift', 'scala', 'lua', 'typescript', 'dart',
    'fortran', 'cobol', 'pascal', 'haskell', 'objectivec',
    'assembly', 'prolog', 'lisp', 'scheme', 'erlang',
    'elixir', 'clojure', 'fsharp', 'vb'
]
```

### File Extensions
```python
extensions = {
    'python': 'py',
    'java': 'java',
    'javascript': 'js',
    'c': 'c',
    'cpp': 'cpp',
    'csharp': 'cs',
    # ... etc
}
```

---

## Security Considerations

### Input Validation
- File size limited to 16MB
- Execution timeout of 30 seconds
- Session-based storage (not persistent)
- No SQL injection (no database)

### Recommendations for Production
1. Change `app.secret_key`
2. Add rate limiting
3. Implement user authentication
4. Add code sandboxing
5. Use HTTPS
6. Add input sanitization
7. Implement CSRF protection
8. Add logging and monitoring

---

## Example Integration

### Complete Workflow Example
```javascript
// 1. Write code
codeEditor.setValue('print("Hello World")');

// 2. Save as snippet
await fetch('/save_snippet', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        name: 'Hello World',
        code: codeEditor.getValue(),
        language: 'python'
    })
});

// 3. Analyze code
const analysis = await fetch('/analyze_code', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        code: codeEditor.getValue(),
        language: 'python'
    })
}).then(res => res.json());

console.log(analysis.stats);

// 4. Share code
const share = await fetch('/share', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        code: codeEditor.getValue(),
        language: 'python',
        input: ''
    })
}).then(res => res.json());

console.log('Share URL:', share.url);

// 5. Execute code (via form submission)
document.getElementById('codeForm').submit();
```

---

## Testing

### Test Endpoints with cURL

**Share Code**:
```bash
curl -X POST http://localhost:5000/share \
  -H "Content-Type: application/json" \
  -d '{"code":"print(\"Hello\")","language":"python","input":""}'
```

**Analyze Code**:
```bash
curl -X POST http://localhost:5000/analyze_code \
  -H "Content-Type: application/json" \
  -d '{"code":"def hello():\n    print(\"Hello\")","language":"python"}'
```

**Get Snippets**:
```bash
curl http://localhost:5000/get_snippets
```

---

## WebSocket Support

Currently not implemented. Future enhancement for:
- Real-time collaboration
- Live code execution
- Interactive debugging

---

## Rate Limiting

Currently not implemented. Recommended for production:
```python
from flask_limiter import Limiter

limiter = Limiter(app, key_func=get_remote_address)

@app.route('/share', methods=['POST'])
@limiter.limit("10 per minute")
def share_code():
    # ...
```

---

## Logging

Add logging for production:
```python
import logging

logging.basicConfig(
    filename='app.log',
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

@app.route('/share', methods=['POST'])
def share_code():
    logging.info(f'Code shared: {code_hash}')
    # ...
```

---

## Version

**API Version**: 2.0.0  
**Last Updated**: November 29, 2025  
**Status**: Stable

---

## Support

For API questions or issues:
- Check FEATURES.md for feature documentation
- See CHANGELOG.md for version history
- Review code in app.py for implementation details

---

**Happy Coding! 🚀**
