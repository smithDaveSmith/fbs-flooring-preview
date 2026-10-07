"""Local preview: python3 serve.py (open http://localhost:8000)."""
from http.server import ThreadingHTTPServer,SimpleHTTPRequestHandler
from pathlib import Path
import os
root=Path(__file__).parent
directory=root/'website' if (root/'website').exists() else root/'dist'
os.chdir(directory)
print('FBS preview: http://localhost:8000')
ThreadingHTTPServer(('127.0.0.1',8000),SimpleHTTPRequestHandler).serve_forever()
