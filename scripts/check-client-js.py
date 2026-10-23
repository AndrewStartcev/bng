#!/usr/bin/env python3
"""Check the actual inline BNG UI JavaScript and both language dictionaries."""
from pathlib import Path
import re
import subprocess
import tempfile

root = Path(__file__).resolve().parents[1]
html = (root / "assets/original/page.html").read_text(encoding="utf-8")
matches = re.findall(r'<script\b[^>]*>(.*?)</script>', html, flags=re.S)
script = next((text for text in reversed(matches) if 'class Component extends DCLogic' in text), None)
if not script:
    raise SystemExit("Missing main client script")
with tempfile.TemporaryDirectory() as temp:
    path = Path(temp) / "bng-client.js"
    path.write_text(script, encoding="utf-8")
    subprocess.run(["node", "--check", str(path)], check=True)
    start = script.index("const COPY = {")
    end = script.index("\n};", start) + len("\n};")
    test = script[start:end] + """
if (!COPY.ru || !COPY.en) throw new Error('Missing RU/EN dictionaries');
for (const locale of ['ru', 'en']) {
  if (!COPY[locale].nav || !COPY[locale].home || !COPY[locale].products) {
    throw new Error('Incomplete ' + locale + ' client dictionary');
  }
}
console.log('Both RU and EN client dictionaries available');
"""
    check = Path(temp) / "bng-languages.js"
    check.write_text(test, encoding="utf-8")
    subprocess.run(["node", str(check)], check=True)
