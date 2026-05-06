#!/usr/bin/env python3
"""
Script seguro para insertar docblocks en español en archivos PHP.

Funcionamiento:
- Recorre directorios objetivo bajo `app/`.
- Para cada archivo `.php` hace un backup `file.php.bak` la primera vez que lo modifica.
- Inserta un docblock antes de cada `class` y de cada método `function` que NO tenga ya un docblock inmediatamente arriba.
- El contenido del docblock es conservador y en español; intenta deducir una descripción corta desde el nombre del método (heurísticas simples).
- Tras modificar archivos valida la sintaxis PHP con `php -l` y revierte cambios si hay errores.

Precauciones: solo modifica comentarios (no toca código). Hacer revisión manual después.
"""

import re
import sys
from pathlib import Path
import shutil
import subprocess

ROOT = Path(__file__).resolve().parents[1]
TARGET_DIRS = [ROOT / 'app' / 'core', ROOT / 'app' / 'controllers', ROOT / 'app' / 'models']

FUNC_RE = re.compile(r'^(\s*)(public|protected|private)?\s*function\s+([a-zA-Z0-9_]+)\s*\(([^)]*)\)\s*\{?', re.MULTILINE)
CLASS_RE = re.compile(r'^(\s*)class\s+([A-Za-z0-9_]+)', re.MULTILINE)

def describe_from_name(name):
    # Heurísticas simples para generar descripciones en español
    lname = name.lower()
    if lname.startswith('get'):
        return f"Obtiene {name[3:].lstrip('_').lower()} y lo devuelve."
    if lname.startswith('set'):
        return f"Establece el valor de {name[3:].lstrip('_').lower()} proporcionado."
    if lname.startswith('is') or lname.startswith('has'):
        return f"Comprueba si {name[2:].lstrip('_').lower()} y devuelve booleano." 
    if lname.startswith('validate'):
        return f"Valida los datos proporcionados para {name[8:].lstrip('_').lower()}."
    if lname in ('index','show','view'):
        return "Método principal de visualización/entrada para esta ruta." 
    if lname in ('redirect','url'):
        return "Realiza una redirección o genera una URL basada en la ruta indicada."
    if lname.startswith('render') or lname.startswith('view'):
        return "Renderiza una vista y pasa los datos asociados." 
    if lname.startswith('save') or lname.startswith('create') or lname.startswith('store'):
        return "Persiste datos en la base de datos relacionados con este recurso." 
    if lname.startswith('delete') or lname.startswith('remove'):
        return "Elimina el recurso indicado en la base de datos." 
    if lname.startswith('login') or lname.startswith('logout') or lname.startswith('register'):
        return "Gestiona la autenticación/registro de usuarios." 
    if lname.startswith('parse') or lname.startswith('to'):
        return "Procesa/transforma la entrada según la lógica del método." 
    return f"Método `{name}` del controlador/clase."

def param_tag_list(param_str):
    params = []
    for p in [p.strip() for p in param_str.split(',') if p.strip()]:
        # forms: $a, Type $b = null
        parts = p.split()
        pname = parts[-1]
        pname = pname.replace('=','').strip()
        params.append(pname)
    return params

def make_docblock_for_method(name, params):
    desc = describe_from_name(name)
    # Construimos docblock más detallado al estilo solicitado
    lines = [
        '/**',
        f' * {desc}',
        ' *',
        ' * Descripción detallada:',
        f' * Este método `{name}` realiza la operación descrita arriba. Se recomienda',
        ' * revisar las validaciones previas y sanitización de los parámetros antes',
        ' * de invocar este método. No modifica el estado global salvo se indique.',
        ' *',
    ]
    if params:
        for p in params:
            pname = p
            # normalizar nombres de parámetros
            pname = pname.strip()
            lines.append(f' * @param mixed {pname} Descripción detallada de {pname}.')
    lines.append(' *')
    lines.append(' * @return mixed Resultado de la operación (tipo dependiendo del contexto).')
    lines.append(' *')
    lines.append(' * Ejemplo de uso:')
    lines.append(f' * ```php')
    example_params = ', '.join([p for p in params]) if params else ''
    lines.append(f' * $this->{name}({example_params});')
    lines.append(' * ```')
    lines.append(' */')
    return '\n'.join(lines)

def make_docblock_for_class(name):
    lines = ['/**', f' * Clase `{name}`', ' * Descripción del propósito de la clase y su responsabilidad principal.', ' */']
    return '\n'.join(lines)

def backup(path: Path):
    bak = path.with_suffix(path.suffix + '.bak')
    if not bak.exists():
        shutil.copy2(path, bak)

def validate_php(path: Path):
    try:
        res = subprocess.run(['php', '-l', str(path)], capture_output=True, text=True)
        return res.returncode == 0, res.stdout + res.stderr
    except FileNotFoundError:
        print('php not found in PATH; no syntax validation performed.', file=sys.stderr)
        return True, 'php not found; skipped'

def process_file(path: Path):
    text = path.read_text(encoding='utf-8')
    orig = text
    modified = False

    # Handle class docblocks
    for m in list(CLASS_RE.finditer(text)):
        cls_name = m.group(2)
        start = m.start()
        # find preceding chars up to start for a docblock
        prefix = text[:start].rstrip()
        if prefix.endswith('*/'):
            continue
        # insert docblock before class
        doc = make_docblock_for_class(cls_name)
        insert_pos = start
        text = text[:insert_pos] + doc + '\n' + text[insert_pos:]
        modified = True

    # Handle methods/functions
    # We'll iterate and rebuild to avoid offset issues
    out_lines = []
    i = 0
    lines = text.splitlines(True)
    total = len(lines)
    while i < total:
        line = lines[i]
        # combine window to match function regex
        window = ''.join(lines[i:i+5])  # enough lines to include signature
        m = FUNC_RE.match(window)
        if m:
            indent, vis, fname, params = m.groups()
            # check previous non-empty line if docblock exists
            prev_idx = i-1
            has_doc = False
            while prev_idx >=0 and lines[prev_idx].strip() == '':
                prev_idx -= 1
            if prev_idx >=0 and lines[prev_idx].strip().startswith('/**'):
                has_doc = True
            if not has_doc:
                param_list = param_tag_list(params)
                doc = make_docblock_for_method(fname, param_list)
                out_lines.append(doc + '\n')
                modified = True
            # append the function line(s) as-is until newline after signature
            out_lines.append(lines[i])
            i += 1
            continue
        else:
            out_lines.append(line)
            i += 1

    new_text = ''.join(out_lines)
    if modified:
        backup(path)
        path.write_text(new_text, encoding='utf-8')
        ok, msg = validate_php(path)
        if not ok:
            print(f"ERROR: sintaxis inválida después de modificar {path}. Revirtiendo desde .bak")
            bak = path.with_suffix(path.suffix + '.bak')
            if bak.exists():
                shutil.copy2(bak, path)
            return False, msg
        return True, 'modificado'
    return True, 'sin cambios'

def main():
    modified_files = []
    for d in TARGET_DIRS:
        if not d.exists():
            continue
        for f in sorted(d.rglob('*.php')):
            ok, msg = process_file(f)
            print(f'{f}: {msg}')
            if ok and msg == 'modificado':
                modified_files.append(f)
            if not ok:
                print('Aborting due to syntax error. Manual review required.')
                sys.exit(1)
    print('\nSummary:')
    print(f'  Archivos modificados: {len(modified_files)}')

if __name__ == '__main__':
    main()
