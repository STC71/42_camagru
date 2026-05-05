#!/usr/bin/env python3
"""
Script seguro para añadir/estandarizar docblocks en español a archivos PHP.
- Añade cabecera de fichero si no existe.
- Añade docblock de clase si no existe.
- Añade docblocks de métodos públicos/protegidos/privados si no existen.

Funciona de forma conservadora: solo inserta comentarios cuando no hay
un docblock inmediatamente anterior; no altera firmas ni cuerpo del código.

USO: python3 tools/add_docblocks.py [ruta]
Si no se indica ruta, procesa `app/`.
"""

import sys
import os
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]

if len(sys.argv) > 1:
    base = Path(sys.argv[1]).resolve()
else:
    base = ROOT / 'app'

php_file_pattern = re.compile(r".*\.php$")

# Regexes
class_re = re.compile(r'^(\s*)class\s+(\w+)', re.MULTILINE)
method_re = re.compile(r'^(\s*)(public|protected|private)\s+function\s+(\w+)\s*\(([^)]*)\)', re.MULTILINE)

file_header_re = re.compile(r'^\s*/\*\*', re.MULTILINE)

def make_file_header(relpath):
    name = relpath.name
    return f"""/**
 * Archivo: {relpath}
 * Descripción: Código fuente del proyecto Camagru — documentación añadida automáticamente (comentarios en español).
 * No modifica lógica, solo añade docblocks explicativos.
 */

"""


def make_class_doc(classname):
    return f"/**\n     * Clase `{classname}`\n     * Descripción: Clase relacionada con {classname} (documentación generada automáticamente en español).\n     */\n"


def param_from_signature(sig):
    sig = sig.strip()
    if not sig:
        return []
    parts = [p.strip() for p in sig.split(',') if p.strip()]
    params = []
    for p in parts:
        # remove default values
        p2 = p.split('=')[0].strip()
        # param might be "Type $name" or "$name"
        if '$' in p2:
            name = p2[p2.find('$'):]
            params.append(name)
    return params


def make_method_doc(method, signature):
    params = param_from_signature(signature)
    lines = ["/**", f"     * Método `{method}`.", f"     * Descripción: Implementación generada automáticamente: {method}."]
    if params:
        for p in params:
            lines.append(f"     * @param mixed {p} Descripción del parámetro {p}")
    lines.append("     * @return mixed")
    lines.append("     */")
    return '\n'.join(lines) + '\n'


def process_file(path: Path):
    text = path.read_text(encoding='utf-8')
    orig = text
    changed = False

    rel = path.relative_to(ROOT)

    # Add file header if missing (only at very top)
    if not file_header_re.search(text[:200]):
        header = make_file_header(rel)
        text = header + text
        changed = True

    # Add class docblocks
    def class_repl(m):
        indent = m.group(1)
        name = m.group(2)
        start = m.start()
        # check if there is a docblock immediately before
        before = text[:start]
        # last 200 chars before start
        snippet = before[-300:]
        if re.search(r"/\*\*[\s\S]*?\*/\s*$", snippet):
            return m.group(0)  # already has docblock
        doc = make_class_doc(name)
        nonlocal changed
        changed = True
        return doc + indent + m.group(0).lstrip()

    # Because we are modifying text while scanning, perform in a careful way
    # We'll find class positions and insert docblocks if missing, using offsets
    new_text = text
    offset = 0
    for cm in class_re.finditer(text):
        start = cm.start() + offset
        name = cm.group(2)
        # check preceding substring
        before = new_text[:start]
        snippet = before[-300:]
        if re.search(r"/\*\*[\s\S]*?\*/\s*$", snippet):
            continue
        doc = make_class_doc(name)
        new_text = new_text[:start] + doc + new_text[start:]
        offset += len(doc)
        changed = True
    text = new_text

    # Methods
    new_text = text
    offset = 0
    for mm in method_re.finditer(text):
        start = mm.start() + offset
        indent = mm.group(1)
        vis = mm.group(2)
        name = mm.group(3)
        sig = mm.group(4)
        # check if docblock exists immediately before
        before = new_text[:start]
        snippet = before[-300:]
        if re.search(r"/\*\*[\s\S]*?\*/\s*$", snippet):
            continue
        doc = make_method_doc(name, sig)
        # indent doc to match
        doc = re.sub(r'^', indent, doc, flags=re.MULTILINE)
        new_text = new_text[:start] + doc + new_text[start:]
        offset += len(doc)
        changed = True
    text = new_text

    if changed:
        path.write_text(text, encoding='utf-8')
    return changed


if __name__ == '__main__':
    modified = []
    for root, dirs, files in os.walk(base):
        for f in files:
            if f.endswith('.php'):
                p = Path(root) / f
                try:
                    if process_file(p):
                        modified.append(p)
                except Exception as e:
                    print(f"[ERROR] {p}: {e}")
    if modified:
        print("Modified files:")
        for p in modified:
            print(p)
    else:
        print("No changes made.")
