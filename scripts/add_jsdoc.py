#!/usr/bin/env python3
import os
import re
import sys
import shutil
from datetime import datetime

# Patterns for function declarations
FUNC_PATTERNS = [
    re.compile(r"^(?P<indent>\s*)function\s+(?P<name>[A-Za-z0-9_]+)\s*\((?P<params>[^)]*)\)\s*\{", re.M),
    re.compile(r"^(?P<indent>\s*)(?:const|let|var)\s+(?P<name>[A-Za-z0-9_]+)\s*=\s*function\s*\((?P<params>[^)]*)\)\s*\{", re.M),
    re.compile(r"^(?P<indent>\s*)(?:const|let|var)\s+(?P<name>[A-Za-z0-9_]+)\s*=\s*\((?P<params>[^)]*)\)\s*=>\s*\{", re.M),
]


def make_jsdoc(name, params):
    lines = []
    lines.append('/**')
    lines.append(f' * {name} - Descripción de la función {name} (añadir detalle).')
    if params.strip():
        lines.append(' *')
        raw = [p.strip() for p in params.split(',') if p.strip()]
        for p in raw:
            # remove default values
            p_name = p.split('=')[0].strip()
            # remove type annotations (TypeScript) if present
            if ':' in p_name:
                p_name = p_name.split(':')[-1].strip()
            lines.append(f' * @param {{any}} {p_name} Descripción de {p_name}.')
    lines.append(' * @returns {any} Descripción del valor devuelto.')
    lines.append(' */')
    return '\n'.join(lines) + '\n'


def process_file(path):
    with open(path, 'r', encoding='utf-8') as f:
        text = f.read()
    modified = False
    new_text = text
    for pat in FUNC_PATTERNS:
        # iterate matches from end to start to not break indexes
        for m in reversed(list(pat.finditer(new_text))):
            start = m.start()
            # check preceding non-empty line for existing JSDoc
            prev_chunk = new_text[:start]
            prev_lines = prev_chunk.rstrip('\n').splitlines()
            prev_nonempty = ''
            for line in reversed(prev_lines):
                if line.strip() == '':
                    continue
                prev_nonempty = line.strip()
                break
            if prev_nonempty.startswith('/**'):
                continue
            indent = m.group('indent') or ''
            name = m.group('name')
            params = m.group('params')
            doc = make_jsdoc(name, params)
            doc_indented = '\n'.join([(indent + line) if line.strip() != '' else line for line in doc.splitlines()]) + '\n'
            new_text = new_text[:start] + doc_indented + new_text[start:]
            modified = True
    if not modified:
        return False, None
    backup = path + '.bak.' + datetime.now().strftime('%Y%m%d%H%M%S')
    shutil.copy2(path, backup)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(new_text)
    return True, backup


def main():
    if len(sys.argv) < 2:
        print('Uso: add_jsdoc.py <dir>')
        sys.exit(1)
    root = sys.argv[1]
    js_files = []
    for dirpath, dirnames, filenames in os.walk(root):
        for fn in filenames:
            if fn.endswith('.js'):
                js_files.append(os.path.join(dirpath, fn))
    modified = []
    backups = []
    for f in js_files:
        try:
            changed, backup = process_file(f)
            if changed:
                modified.append(f)
                backups.append((f, backup))
                print(f'Modified: {f} (backup: {backup})')
        except Exception as e:
            print(f'Error processing {f}: {e}')
    if not modified:
        print('No se modificaron archivos JS.')
    else:
        print('Modificados:', len(modified))

if __name__ == '__main__':
    main()
