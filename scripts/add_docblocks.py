#!/usr/bin/env python3
import os
import re
import sys
import shutil
from datetime import datetime

FUNC_RE = re.compile(r"^(?P<indent>\s*)(?P<visibility>public|protected|private)?\s*function\s+(?P<name>[A-Za-z0-9_]+)\s*\((?P<params>[^)]*)\)\s*(?P<suffix>[:\s\w\\]*)\{", re.MULTILINE)

DESCRIPTION_MAP = {
    'model': "Carga y devuelve una instancia del modelo solicitado.",
    'view': "Renderiza una vista pasando datos a su scope.",
    'redirect': "Redirige a la URL proporcionada.",
    'url': "Construye la URL completa basada en la configuración de la aplicación.",
    'json': "Envía una respuesta JSON y finaliza la ejecución.",
    'isPost': "Comprueba si la petición actual es de tipo POST.",
    'isGet': "Comprueba si la petición actual es de tipo GET.",
    'validateCSRF': "Valida el token CSRF enviado en el formulario contra el token en sesión.",
    'generateCSRF': "Genera y devuelve un token CSRF almacenado en la sesión si no existe.",
    'sanitize': "Sanea recursivamente una entrada para evitar XSS y etiquetas.",
    'requireAuth': "Fuerza que la ruta requiera autenticación.",
    'requireGuest': "Fuerza que la ruta sea accesible solo por invitados (no autenticados).",
    'dispatch': "Despacha la petición actual resolviendo controlador, método y parámetros.",
    'parseUrl': "Parsea la URL proporcionada y devuelve sus partes.",
    'toCamelCase': "Convierte una cadena en kebab-case a camelCase.",
}


def make_docblock(name, params):
    desc = DESCRIPTION_MAP.get(name, f"Método {name}.")
    lines = []
    lines.append('/**')
    lines.append(f' * {desc}')
    if params.strip():
        lines.append(' *')
    # params: split by comma, handle default values and type hints
    param_list = []
    if params.strip():
        raw_params = [p.strip() for p in params.split(',') if p.strip()]
        for p in raw_params:
            # remove type hints and defaults
            # examples: "$id", "string $name", "$a = null", "?int $x = 0"
            parts = p.split()
            name_part = parts[-1]
            # remove default
            name_only = name_part.split('=')[0].strip()
            name_only = name_only.lstrip('&').strip()
            if name_only.startswith('$'):
                param_name = name_only
            else:
                # fallback
                param_name = '$param'
            param_list.append(param_name)
            lines.append(f' * @param mixed {param_name} Descripción del parámetro {param_name}.')
    lines.append(' * @return mixed')
    lines.append(' */')
    return '\n'.join(lines) + '\n'


def process_file(path):
    with open(path, 'r', encoding='utf-8') as f:
        text = f.read()

    new_text = []
    last_index = 0
    modified = False
    for m in FUNC_RE.finditer(text):
        start = m.start()
        # find preceding non-empty, non-whitespace line
        prefix = text[last_index:start]
        # determine if there's a docblock immediately before
        # get substring from last newline before start
        prev_chunk = text[:start]
        prev_lines = prev_chunk.rstrip('\n').splitlines()
        prev_nonempty = ''
        if prev_lines:
            # check last non-empty line
            for line in reversed(prev_lines):
                if line.strip() == '':
                    continue
                prev_nonempty = line.strip()
                break
        has_doc = prev_nonempty.startswith('/**')
        if has_doc:
            # skip insertion
            continue
        # else insert docblock at position of start
        indent = m.group('indent') or ''
        name = m.group('name')
        params = m.group('params')
        doc = make_docblock(name, params)
        # maintain indentation for docblock
        doc_indented = '\n'.join([(indent + line) if line.strip() != '' else line for line in doc.splitlines()]) + '\n'
        new_text.append(text[last_index:start])
        new_text.append(doc_indented)
        last_index = start
        modified = True
    new_text.append(text[last_index:])
    if not modified:
        return False, None
    backup_path = path + '.bak.' + datetime.now().strftime('%Y%m%d%H%M%S')
    shutil.copy2(path, backup_path)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(''.join(new_text))
    return True, backup_path


def php_syntax_check(files):
    import subprocess
    errors = {}
    for f in files:
        p = subprocess.run(['php', '-l', f], capture_output=True, text=True)
        if p.returncode != 0:
            errors[f] = p.stdout + p.stderr
    return errors


def main():
    if len(sys.argv) < 2:
        print('Uso: add_docblocks.py <dir>')
        sys.exit(1)
    root = sys.argv[1]
    php_files = []
    for dirpath, dirnames, filenames in os.walk(root):
        for fn in filenames:
            if fn.endswith('.php'):
                php_files.append(os.path.join(dirpath, fn))
    modified_files = []
    backups = []
    for pf in php_files:
        try:
            changed, backup = process_file(pf)
            if changed:
                modified_files.append(pf)
                backups.append((pf, backup))
                print(f'Modified: {pf} (backup: {backup})')
        except Exception as e:
            print(f'Error processing {pf}: {e}')
    if not modified_files:
        print('No se modificaron archivos.')
        return
    print('\nValidando sintaxis PHP de archivos modificados...')
    errors = php_syntax_check(modified_files)
    if errors:
        print('Se detectaron errores de sintaxis. Revirtiendo backups...')
        for orig, backup in backups:
            try:
                shutil.copy2(backup, orig)
                print(f'Revertido: {orig} <- {backup}')
            except Exception as e:
                print(f'No se pudo revertir {orig}: {e}')
        print('Lista de errores:')
        for f, out in errors.items():
            print(f'--- {f} ---\n{out}')
        sys.exit(2)
    else:
        print('Validación completada. Todos los archivos PHP son sintácticamente válidos.')
        print('Archivos modificados:')
        for f in modified_files:
            print(' -', f)

if __name__ == '__main__':
    main()
