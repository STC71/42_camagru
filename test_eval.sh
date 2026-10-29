#!/usr/bin/env bash
# =============================================================================
# Camagru · test_eval.sh
# Guía interactiva de defensa alineada con la hoja oficial de evaluación
# (en.eval / Camagru · 42 Common Core)
#
# Uso:
#   chmod +x test_eval.sh
#   ./test_eval.sh
#
# Pensado para ejecutar CON el evaluador: explica qué mirar, dónde está el
# código y qué debe ocurrir en el navegador. No sustituye el criterio humano.
# Campus: 42 Málaga · https://www.42malaga.com/
# =============================================================================

set -u

R='\033[0;31m'; G='\033[0;32m'; Y='\033[1;33m'
B='\033[0;34m'; C='\033[0;36m'; M='\033[0;35m'
W='\033[1;37m'; D='\033[2m'; N='\033[0m'; BOLD='\033[1m'

ROOT="$(cd "$(dirname "$0")" && pwd)"
cd "$ROOT" || exit 1
APP_URL="${APP_URL:-http://localhost:8080}"

pause() {
  echo ""
  echo -e "${C}▸ Pulsa [ENTER] para continuar…${N}"
  read -r _
}

header() {
  clear 2>/dev/null || true
  echo -e "${B}╭────────────────────────────────────────────────────────────────────╮${N}"
  echo -e "${B}│${W}${BOLD}              CAMAGRU · GUÍA DE EVALUACIÓN (DEFENSA)                ${N}${B}│${N}"
  echo -e "${B}│${N}   Hoja oficial + subject v4.1 · objetivo 125 % (mandatory+bonus)   ${B}│${N}"
  echo -e "${B}│${N}          42 Málaga · sternero · https://www.42malaga.com           ${B}│${N}"
  echo -e "${B}╰────────────────────────────────────────────────────────────────────╯${N}"
  echo -e "  ${D}${APP_URL}  ·  ${ROOT}${N}"
  echo ""
}

step() {
  header
  echo -e "${M}${BOLD}▶ $1${N}"
  echo -e "${D}$2${N}"
  echo ""
}

find_code() {
  local label="$1"
  local pattern="$2"
  shift 2
  echo -e "${Y}🔍 Código · ${label}${N}"
  local any=0
  for file in "$@"; do
    if [[ -f "$file" ]]; then
      local lines
      lines=$(grep -nE "$pattern" "$file" 2>/dev/null | head -8 | cut -d: -f1 | paste -sd, -)
      if [[ -n "$lines" ]]; then
        echo -e "   ${C}${file}${N}  líneas ${G}${lines}${N}"
        any=1
      fi
    fi
  done
  [[ $any -eq 0 ]] && echo -e "   ${R}(no se encontró el patrón en los ficheros indicados)${N}"
}

check_file() {
  local f="$1"
  if [[ -f "$f" ]]; then
    echo -e "   ${G}✓${N} $f"
  else
    echo -e "   ${R}✗${N} $f ${D}(ausente)${N}"
  fi
}

# ── Intro ────────────────────────────────────────────────────────────────────
header
echo -e "${W}Esta guía sigue el orden de la hoja de evaluación de Camagru.${N}"
echo -e "En cada paso verás:"
echo -e "  ${G}1.${N} Qué debe comprobar el evaluador (criterio oficial)."
echo -e "  ${G}2.${N} Qué hacer en el navegador o terminal."
echo -e "  ${G}3.${N} Dónde está el código relevante en este repo."
echo ""
echo -e "${Y}Reglas recordatorio (eval):${N}"
echo -e "  · Solo se evalúa lo que está en el repositorio Git entregado."
echo -e "  · Errores en consola del navegador o logs del servidor (salvo getUserMedia) → Crash."
echo -e "  · Los bonus solo cuentan si el mandatory funciona sin fallos."
echo -e "  · Cortesía y rigor: https://www.42malaga.com/"
pause

# ── 0 Preliminaries / Preflight ──────────────────────────────────────────────
step "0 · PREFLIGHT CHECK" \
  "Si algo de esto falla, la evaluación puede detenerse (Cheat / Crash)."

echo -e "${W}Criterios oficiales:${N}"
echo -e "  · Aplicación en ${BOLD}PHP${N}, ${BOLD}sin frameworks${N} ni micro-frameworks."
echo -e "  · ${BOLD}Sin${N} package managers (npm, composer…)."
echo -e "  · Presentes: index.php, config de BD / setup."
echo -e "  · Consultas vía ${BOLD}PDO${N} con ${BOLD}PDO::ERRMODE_EXCEPTION${N}."
echo ""
echo -e "${W}En este proyecto:${N}"
check_file "public/index.php"
check_file "app/core/Database.php"
check_file "config/Config.php"
check_file "database/init.sql"
check_file "Dockerfile"
check_file "docker-compose.yml"
echo ""
find_code "PDO + ERRMODE_EXCEPTION" "ERRMODE_EXCEPTION|new PDO" \
  app/core/Database.php config/database.php config/setup.php
echo ""
echo -e "${W}Comando útil:${N}"
echo -e "  ${C}./test_auto.sh --section preflight${N}"
pause

# ── 1 Ignition ───────────────────────────────────────────────────────────────
step "1 · 3..2..1.. IGNITION" \
  "Arrancar el servidor y abrir la app sin errores."

echo -e "${W}Qué hacer:${N}"
echo -e "  ${C}docker-compose up --build -d${N}"
echo -e "  Abrir ${G}${APP_URL}${N} — debe cargar sin error fatal."
echo -e "  Mirar consola del navegador y logs:"
echo -e "  ${C}docker-compose logs web --tail 30${N}"
echo ""
echo -e "${Y}Eval:${N} si el servidor no arranca o la app no sirve «as is» → este apartado NO."
echo -e "Sugerencia: ${C}./test_auto.sh --section http${N}"
pause

# ── 2 User Creation ──────────────────────────────────────────────────────────
step "2 · USER CREATION" \
  "Registro con username, password seguro y email + mail de confirmación."

echo -e "${W}En el navegador:${N}"
echo -e "  1. Ir a ${G}${APP_URL}/register${N}"
echo -e "  2. Probar password débil (solo minúsculas) → debe rechazarse."
echo -e "  3. Registro válido → mensaje de éxito + correo (Mailtrap)."
echo -e "  4. Sin verificar el enlace → ${BOLD}no${N} debe poder iniciar sesión."
echo -e "  5. Abrir el enlace único del mail → cuenta verificada."
echo ""
find_code "Registro + validación" "function register|Validator::password|password_hash" \
  app/controllers/AuthController.php app/core/Validator.php app/models/User.php
find_code "Envío de correo de verificación" "verify|Mailer|send" \
  app/controllers/AuthController.php app/core/Mailer.php
pause

# ── 3 Authentication ─────────────────────────────────────────────────────────
step "3 · USER AUTHENTICATION" \
  "Login, logout en cualquier página, reset de password."

echo -e "${W}En el navegador:${N}"
echo -e "  1. Login con usuario verificado."
echo -e "  2. Logout visible desde varias páginas (header)."
echo -e "  3. Forgot password → mail con enlace → nueva contraseña → login."
echo ""
find_code "Login / Logout / Forgot" "function login|function logout|forgot|reset" \
  app/controllers/AuthController.php
echo -e "${W}UI:${N} badge de sesión y menú en el header."
pause

# ── 4 Editing (ft_snapchat) ──────────────────────────────────────────────────
step "4 · EDITING (ft_snapchat.php)" \
  "Webcam o upload, stickers, captura solo con lógica de servidor."

echo -e "${W}Criterios clave:${N}"
echo -e "  · Vista con header / main / footer."
echo -e "  · Preview webcam + lista de stickers + miniaturas previas."
echo -e "  · Opción de subir imagen base además de webcam."
echo -e "  · Botón de captura inactivo sin sticker (subject) / o casos 0..N stickers según interpretación aceptada."
echo -e "  · ${BOLD}Composición final en el servidor${N} (no solo canvas cliente)."
echo ""
echo -e "${W}En el navegador:${N}"
echo -e "  1. ${G}${APP_URL}/editor${N} sin sesión → redirige a login."
echo -e "  2. Con sesión: Iniciar cámara → elegir sticker → Capturar."
echo -e "  3. Comprobar que la foto aparece en miniaturas / galería / perfil."
echo -e "  4. Subir archivo como base + sticker → guardar."
echo ""
find_code "Captura server-side" "function capture|imagecopy|mergeImages|imagecreatefromstring" \
  app/controllers/EditorController.php
find_code "Cliente editor (webcam/stickers)" "getUserMedia|sticker|capture" \
  public/js/editor.js
pause

# ── 5 Gallery ────────────────────────────────────────────────────────────────
step "5 · GALLERY FEATURES" \
  "Pública, orden por fecha, likes/comentarios si hay sesión, paginación ≥5."

echo -e "${W}En el navegador:${N}"
echo -e "  1. ${G}${APP_URL}/gallery${N} sin login → se ven imágenes."
echo -e "  2. Con login → like y comentario."
echo -e "  3. Comentario → email al autor si tiene preferencia activa (Mailtrap)."
echo -e "  4. Paginación o scroll con al menos 5 elementos por página."
echo ""
find_code "Galería / paginación" "perPage|getAll|load-more|function index" \
  app/controllers/GalleryController.php public/js/gallery.js
find_code "Likes y comentarios" "like|comment" \
  app/controllers/GalleryController.php
pause

# ── 6 Preferences & permissions ──────────────────────────────────────────────
step "6 · PREFERENCIAS Y PERMISOS (Cancancan)" \
  "Perfil, password, notificaciones; borrar solo lo propio; editor protegido."

echo -e "${W}En el navegador:${N}"
echo -e "  1. Perfil / Ajustes: cambiar username, email, password."
echo -e "  2. Activar/desactivar notificaciones de comentarios."
echo -e "  3. Borrar una foto propia (galería o editor) — no la de otro usuario."
echo -e "  4. Ventana privada → /editor → login."
echo ""
find_code "requireAuth" "requireAuth|requireGuest" \
  app/core/Controller.php app/controllers/EditorController.php
find_code "Delete propio" "function delete" app/controllers/EditorController.php
pause

# ── 7 Security ───────────────────────────────────────────────────────────────
step "7 · SECURITY FIRST DAMNIT!" \
  "El apartado más crítico de la hoja."

echo -e "${R}${BOLD}1. Contraseñas${N}"
echo -e "   Deben estar hasheadas (password_hash / bcrypt). Nunca en claro en BD."
find_code "Hash" "password_hash|password_verify" app/models/User.php app/controllers/AuthController.php
echo ""
echo -e "${R}${BOLD}2. XSS${N}"
echo -e "   Comentar: ${C}<script>alert('xss')</script>${N}"
echo -e "   Debe verse como texto, no ejecutarse."
find_code "Escape HTML" "htmlspecialchars" app/views/gallery/index.php app/core/Controller.php
echo ""
echo -e "${R}${BOLD}3. SQL Injection${N}"
echo -e "   Login con password: ${C}blahblah' OR '1'='1${N} → no debe entrar."
find_code "Prepared statements" "prepare\(|ERRMODE_EXCEPTION" app/core/Database.php app/models/*.php
echo ""
echo -e "${Y}También vigilar:${N} uploads maliciosos, CSRF en POST sensibles."
find_code "CSRF" "csrf_token|validateCSRF" app/core/Controller.php app/controllers/*.php
pause

# ── 8 UI/UX mobile ───────────────────────────────────────────────────────────
step "8 · UI / UX / RESPONSIVE" \
  "La hoja exige una interfaz usable; móvil en DevTools."

echo -e "${W}Qué hacer:${N}"
echo -e "  · Chrome DevTools → toggle device toolbar."
echo -e "  · Home, Gallery, Editor, Login en ~375px de ancho."
echo -e "  · Header legible; botones alcanzables; sin desbordes graves."
echo ""
echo -e "CSS principal: ${C}public/css/style.css${N}"
pause

# ── 9 Bonus ──────────────────────────────────────────────────────────────────
step "9 · BONUS (solo si mandatory perfecto)" \
  "AJAX, preview en vivo, infinite scroll, GIF, etc."

echo -e "  ${G}★${N} AJAX en likes/comentarios (sin recargar toda la página)."
echo -e "  ${G}★${N} Preview en vivo del sticker sobre la webcam (+ drag)."
echo -e "  ${G}★${N} Infinite scroll o carga progresiva en galería."
echo -e "  ${G}★${N} GIF animado (Imagick) — demo en editor."
echo ""
echo -e "${W}Comprobar GIF en runtime:${N}"
echo -e "  ${C}docker-compose exec web php -m | grep -i imagick${N}"
echo -e "  Editor → sticker → Grabar GIF → Network → create-gif → success:true"
echo ""
find_code "GIF server-side" "createGif|Imagick|readImageBlob" app/controllers/EditorController.php
find_code "AJAX gallery" "fetch\(|load-more" public/js/gallery.js
find_code "Live preview / drag" "stickerPosition|getUserMedia|mousedown" public/js/editor.js
pause

# ── Cierre ───────────────────────────────────────────────────────────────────
header
echo -e "${G}${BOLD}Fin de la guía de evaluación.${N}"
echo ""
echo -e "Checklist rápido post-defensa:"
echo -e "  ${C}./test_auto.sh${N}          → batería automática"
echo -e "  ${C}./test_auto.sh --quick${N}  → solo críticos"
echo -e "  ${C}docker-compose logs web --tail 50${N}"
echo ""
echo -e "Limpieza opcional del entorno de demo:"
echo -e "  ${C}docker-compose down${N}     (sin -v conserva la BD)"
echo -e "  ${C}docker-compose down -v${N}  (borra volúmenes — ¡destructivo!)"
echo ""
echo -e "${D}Hecho con rigor para Camagru · 42 Málaga · https://www.42malaga.com/${N}"
echo -e "${D}Los scripts ayudan; la nota la decide la defensa humana según el subject.${N}"
echo ""
exit 0
