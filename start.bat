@echo off
echo ========================================
echo Multi-Language Online IDE
echo ========================================
echo.

REM Check if Flask is installed
python -c "import flask" 2>nul
if errorlevel 1 (
    echo Flask is not installed. Installing...
    pip install flask
    echo.
)

echo Starting the application...
echo Open your browser to: http://127.0.0.1:5000
echo Press Ctrl+C to stop the server
echo.
python app.py
