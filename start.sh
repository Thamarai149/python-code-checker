#!/bin/bash

echo "========================================"
echo "Multi-Language Online IDE"
echo "========================================"
echo ""

# Check if Flask is installed
if ! python3 -c "import flask" 2>/dev/null; then
    echo "Flask is not installed. Installing..."
    pip3 install flask
    echo ""
fi

echo "Starting the application..."
echo "Open your browser to: http://127.0.0.1:5000"
echo "Press Ctrl+C to stop the server"
echo ""
python3 app.py
