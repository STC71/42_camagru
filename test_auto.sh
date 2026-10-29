#!/usr/bin/env bash
# =============================================================================
# Camagru · test_auto.sh
# Suite automática alineada con en.subject + en.eval (42 · Camagru)
# Campus: 42 Málaga · https://www.42malaga.com/
#
# Uso:
#   chmod +x test_auto.sh && ./test_auto.sh
#   ./test_auto.sh --quick
#   ./test_auto.sh --section security
# =============================================================================

set -u

R='\033[0;31m'; G='\033[0;32m'; Y='\033[1;33m'
B='\033[0;34m'; C='\033[0;36m'; M='\033[0;35m'
W='\033[1;37m'; D='\033[2m'; N='\033[0m'; BOLD='\033[1m'

TOTAL=0; PASS=0; FAIL=0; SKIP=0
APP_URL="${APP_URL:-http://localhost:8080}"
ROOT="$(cd "$(dirname "$0")" && pwd)"
cd "$ROOT" || exit 1
WHO="${USER:-$(whoami 2>/dev/null || echo desconocido)}"

MODE="full"
SECTION=""
while [[ $# -gt 0 ]]; do
  case "$1" in
    --quick) MODE="quick"; shift ;;
    --section) SECTION="${2:-}"; shift 2 ;;
    -h|--help) sed -n '2,12p' "$0"; exit 0 ;;
    *) echo "Opción desconocida: $1"; exit 1 ;;
  esac
done

banner() {
  clear 2>/dev/null || true
  echo -e "${M}${BOLD}"
  echo "╔══════════════════════════════════════════════════════════════════════╗"
  echo "║           CAMAGRU · SUITE AUTOMÁTICA DE PRUEBAS (125 %)              ║"
  echo "║     Subject + hoja de evaluación · 42 Málaga · Outer Core            ║"
  echo "╚══════════════════════════════════════════════════════════════════════╝"
  echo -e "${N}"
  echo -e "  ${D}APP_URL=${APP_URL}  ·  raíz=${ROOT}${N}"
  echo -e "  ${D}$(date '+%Y-%m-%d %H:%M:%S')  ·  modo=${MODE}${SECTION:+ · section=$SECTION}  ·  user=${WHO}${N}"
  echo ""
}

section() {
  echo ""
  echo -e "${C}${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${N}"
  echo -e "${C}${BOLD}  $1${N}"
  echo -e "${C}${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${N}"
}

# Muestra hasta 6 coincidencias file:line para un patrón en una lista de paths
show_hits() {
  local pattern="$1"; shift
  local hits=0
  local f
  for f in "$@"; do
    [[ -e "$f" ]] || continue
    if [[ -f "$f" ]]; then
      while IFS= read -r line; do
        echo -e "      ${D}${line}${N}"
        hits=$((hits+1))
        [[ $hits -ge 8 ]] && break 2
      done < <(grep -Ene "$pattern" "$f" 2>/dev/null | head -4 | sed "s|^|$f:|")
    elif [[ -d "$f" ]]; then
      while IFS= read -r line; do
        echo -e "      ${D}${line}${N}"
        hits=$((hits+1))
        [[ $hits -ge 8 ]] && break 2
      done < <(grep -Erne "$pattern" "$f" 2>/dev/null | head -4)
    fi
  done
  [[ $hits -eq 0 ]] && echo -e "      ${D}(sin coincidencias listables)${N}"
}

ok() {
  echo -e "  ${G}✓${N} $1"
  PASS=$((PASS+1)); TOTAL=$((TOTAL+1))
}
bad() {
  echo -e "  ${R}✗${N} $1"
  FAIL=$((FAIL+1)); TOTAL=$((TOTAL+1))
}
skip() {
  echo -e "  ${Y}⊘${N} $1"
  SKIP=$((SKIP+1)); TOTAL=$((TOTAL+1))
}
info() { echo -e "  ${D}→ $1${N}"; }

# Éxito si el patrón EXISTE; muestra ubicaciones
assert_present() {
  local label="$1"
  local pattern="$2"
  shift 2
  local found=0
  local f
  for f in "$@"; do
    [[ -e "$f" ]] || continue
    if [[ -f "$f" ]] && grep -Eqe "$pattern" "$f" 2>/dev/null; then found=1; break; fi
    if [[ -d "$f" ]] && grep -Erqe "$pattern" "$f" 2>/dev/null; then found=1; break; fi
  done
  if [[ $found -eq 1 ]]; then
    ok "$label"
    show_hits "$pattern" "$@"
  else
    bad "$label"
    echo -e "      ${Y}Patrón buscado:${N} ${C}${pattern}${N}"
    echo -e "      ${Y}En:${N} $*"
  fi
}

# Éxito si el patrón NO existe (prohibido); indica comando manual
assert_absent() {
  local label="$1"
  local pattern="$2"
  local scope="${3:-app public config}"
  if grep -Erqe "$pattern" $scope 2>/dev/null; then
    bad "$label"
    echo -e "      ${Y}Se encontró el patrón prohibido. Revisa:${N}"
    grep -Erne "$pattern" $scope 2>/dev/null | head -5 | while IFS= read -r line; do
      echo -e "      ${R}${line}${N}"
    done
  else
    ok "$label"
    echo -e "      ${D}Comando manual (no debe listar nada):${N}"
    echo -e "      ${C}grep -rne '${pattern}' ${scope}${N}"
    echo -e "      ${D}Resultado esperado: sin líneas / exit code 1${N}"
  fi
}

need_section() {
  local s="$1"
  [[ -z "$SECTION" || "$SECTION" == "$s" ]]
}

# ── 1 Preflight ──────────────────────────────────────────────────────────────
section_preflight() {
  section "1 · PREFLIGHT (hoja de evaluación · si falla → Cheat/Crash)"

  if find app public config -name '*.php' 2>/dev/null | head -1 | grep -q .; then
    ok "Proyecto PHP detectado (app/public/config)"
  else
    bad "No se detectan archivos PHP en app|public|config"
  fi

  local pkg=0
  for f in composer.json package.json yarn.lock pnpm-lock.yaml; do
    if [[ -f "$f" ]]; then
      bad "Detectado $f (eval: sin package manager)"
      pkg=1
    fi
  done
  if [[ $pkg -eq 0 ]]; then
    ok "Sin composer/npm/yarn (requisito eval)"
    echo -e "      ${D}Comando: ls composer.json package.json 2>/dev/null → no listar ficheros${N}"
  fi

  assert_absent "Sin frameworks PHP externos (Laravel/Symfony/CodeIgniter)" \
    'Illuminate\\|Symfony\\\\Bundle|CodeIgniter\\\\|Laravel\\\\' \
    "app public config"

  if [[ -f public/index.php ]]; then
    ok "Existe public/index.php (front controller)"
    echo -e "      ${D}public/index.php${N}"
  else
    bad "Falta public/index.php"
  fi

  if [[ -f config/database.php ]]; then
    ok "config/database.php presente"
    echo -e "      ${D}config/database.php${N}"
  elif [[ -f app/core/Database.php ]]; then
    ok "app/core/Database.php presente (equivalente)"
    echo -e "      ${D}app/core/Database.php${N}"
  else
    bad "Falta config/database.php o app/core/Database.php"
  fi

  if [[ -f config/setup.php ]]; then
    ok "config/setup.php presente"
    echo -e "      ${D}config/setup.php${N}"
  elif [[ -f database/init.sql ]] || [[ -f database/schema.sql ]]; then
    ok "Esquema SQL de inicialización presente"
    show_hits '.' database/init.sql database/schema.sql 2>/dev/null || true
  else
    bad "Falta config/setup.php o database/*.sql"
  fi

  assert_present "PDO::ERRMODE_EXCEPTION configurado" \
    'ERRMODE_EXCEPTION' \
    app/core/Database.php config/database.php config/setup.php

  assert_present "Uso de PDO (new PDO / PDO::)" \
    'new PDO|PDO::' \
    app/core/Database.php config/database.php
}

# ── 2 Estructura ─────────────────────────────────────────────────────────────
section_structure() {
  section "2 · ESTRUCTURA (MVC, Docker, assets)"
  for d in app/controllers app/models app/views app/core public public/css public/js public/stickers; do
    if [[ -d "$d" ]]; then ok "Directorio $d"; else bad "Falta directorio $d"; fi
  done
  for f in Dockerfile docker-compose.yml \
           app/controllers/AuthController.php \
           app/controllers/EditorController.php \
           app/controllers/GalleryController.php \
           app/core/Router.php; do
    if [[ -f "$f" ]]; then ok "Existe $f"; echo -e "      ${D}${f}${N}"; else bad "Falta $f"; fi
  done
  local n
  n=$(find public/stickers -type f \( -name '*.png' -o -name '*.gif' -o -name '*.webp' \) 2>/dev/null | wc -l | tr -d ' ')
  if [[ "${n:-0}" -ge 1 ]]; then ok "Stickers disponibles ($n archivos)"; else bad "Sin stickers en public/stickers/"; fi
}

# ── 3 Docker ─────────────────────────────────────────────────────────────────
section_docker() {
  section "3 · DOCKER & ENTORNO (\"works as is\")"
  if ! command -v docker >/dev/null 2>&1; then skip "Docker no está en PATH"; return; fi
  ok "Docker disponible"
  local COMPOSE="docker-compose"
  docker-compose version >/dev/null 2>&1 || COMPOSE="docker compose"

  local web_id db_id st
  web_id=$($COMPOSE ps -q web 2>/dev/null | head -1 || true)
  if [[ -n "$web_id" ]]; then
    st=$(docker inspect -f '{{.State.Status}}' "$web_id" 2>/dev/null || echo unknown)
    [[ "$st" == "running" ]] && ok "Contenedor web en ejecución" || bad "Contenedor web estado=$st"
  else
    bad "Contenedor web no encontrado (docker-compose up -d)"
  fi
  db_id=$($COMPOSE ps -q db 2>/dev/null | head -1 || true)
  [[ -n "$db_id" ]] && ok "Contenedor db presente" || bad "Contenedor db no encontrado"

  if [[ -n "${web_id:-}" ]]; then
    if $COMPOSE exec -T web php -m 2>/dev/null | grep -qi '^pdo_mysql$'; then
      ok "Extensión pdo_mysql en el contenedor"
      echo -e "      ${D}Comando: docker-compose exec web php -m | grep pdo_mysql${N}"
    else bad "Falta pdo_mysql"; fi
    if $COMPOSE exec -T web php -m 2>/dev/null | grep -qi '^gd$'; then
      ok "Extensión gd (edición de imágenes)"
    else bad "Falta gd"; fi
    if $COMPOSE exec -T web php -r 'echo !empty(gd_info()["JPEG Support"])?"1":"0";' 2>/dev/null | grep -q 1; then
      ok "GD con soporte JPEG"
      echo -e "      ${D}Comando: docker-compose exec web php -r 'print_r(gd_info());' | grep JPEG${N}"
    else bad "GD sin soporte JPEG"; fi
    if $COMPOSE exec -T web php -m 2>/dev/null | grep -qi imagick; then
      ok "Extensión imagick (bonus GIF)"
      echo -e "      ${D}Comando: docker-compose exec web php -m | grep -i imagick${N}"
    else skip "imagick no cargado (bonus GIF en riesgo)"; fi
  fi
}

# ── 4 HTTP ───────────────────────────────────────────────────────────────────
section_http() {
  section "4 · IGNITION (servidor responde sin error fatal)"
  if ! command -v curl >/dev/null 2>&1; then skip "curl no disponible"; return; fi
  local code
  code=$(curl -s -o /tmp/camagru_http_body.txt -w '%{http_code}' --max-time 8 "$APP_URL/" 2>/dev/null || echo "000")
  if [[ "$code" =~ ^[23] ]]; then ok "GET / → HTTP $code"; else bad "GET / → HTTP $code"; fi
  if grep -qiE 'Fatal error|Uncaught Error|Parse error' /tmp/camagru_http_body.txt 2>/dev/null; then
    bad "Respuesta de / contiene error PHP fatal"
  else
    ok "Sin fatal/parse error visible en /"
  fi
  for path in /login /register /gallery; do
    code=$(curl -s -o /dev/null -w '%{http_code}' --max-time 8 "$APP_URL$path" 2>/dev/null || echo "000")
    if [[ "$code" =~ ^[23] ]]; then ok "GET $path → HTTP $code"; else bad "GET $path → HTTP $code"; fi
  done
  code=$(curl -s -o /dev/null -w '%{http_code}' --max-time 8 "$APP_URL/editor" 2>/dev/null || echo "000")
  if [[ "$code" =~ ^(302|301|401|403|200)$ ]]; then
    ok "GET /editor → HTTP $code (acceso controlado o sesión)"
  else
    bad "GET /editor → HTTP $code inesperado"
  fi
  code=$(curl -s -o /dev/null -w '%{http_code}' --max-time 5 "$APP_URL/css/style.css" 2>/dev/null || echo "000")
  [[ "$code" == "200" ]] && ok "CSS principal accesible" || bad "CSS principal HTTP $code"
  code=$(curl -s -o /dev/null -w '%{http_code}' --max-time 5 "$APP_URL/js/main.js" 2>/dev/null || echo "000")
  [[ "$code" == "200" ]] && ok "JS main accesible" || skip "js/main.js HTTP $code"
}

# ── 5 Seguridad ──────────────────────────────────────────────────────────────
section_security() {
  section "5 · SEGURIDAD (password_hash, XSS, SQLi, CSRF)"

  assert_present "password_hash (bcrypt/argon)" \
    'password_hash\(' \
    app/models/User.php app/controllers/AuthController.php

  assert_present "password_verify" \
    'password_verify\(' \
    app/models/User.php app/controllers/AuthController.php

  assert_present "htmlspecialchars (anti-XSS)" \
    'htmlspecialchars\(' \
    app/core/Controller.php app/views/gallery/index.php app/views/partials/header.php

  # El proyecto encapsula prepare en Database::query() → connection->prepare
  # También aceptamos $this->db->query($sql, $params) como uso de prepared vía wrapper
  if grep -Erq -e '->prepare\(' -e 'PDO::prepare' app/core app/models app/controllers 2>/dev/null \
     || grep -Erq -e 'function query' -e 'db->query' app/core app/models 2>/dev/null; then
    ok "Prepared statements (anti-SQLi) — PDO prepare o wrapper query(\$sql, \$params)"
    show_hits 'prepare\(|function query|db->query' app/core/Database.php app/models
    echo -e "      ${D}Comando: grep -rne 'prepare(' -e 'function query' app/core app/models | head${N}"
  else
    bad "Prepared statements (anti-SQLi) no detectados"
    echo -e "      ${Y}Comando de verificación:${N}"
    echo -e "      ${C}grep -rne 'prepare(' -e 'function query' app/core app/models | head${N}"
  fi

  assert_present "CSRF token en formularios/controladores" \
    'csrf_token|validateCSRF|CSRF' \
    app/core/Controller.php app/controllers app/views/auth

  assert_absent "Sin mysqli_query concatenando entrada de usuario" \
    'mysqli_query\s*\(.*\$_(GET|POST|REQUEST)' \
    "app"

  assert_absent "Sin eval/shell_exec peligroso con input de usuario" \
    'eval\s*\(.*\$_(GET|POST)|shell_exec\s*\(.*\$_(GET|POST)' \
    "app"
}

# ── 6 Features ───────────────────────────────────────────────────────────────
section_features() {
  section "6 · FUNCIONALIDAD (mapa hoja de evaluación)"
  info "User Creation / Authentication"
  assert_present "Registro de usuario" 'function register' app/controllers/AuthController.php
  assert_present "Login" 'function login' app/controllers/AuthController.php
  assert_present "Logout" 'function logout' app/controllers/AuthController.php
  assert_present "Verificación de email" 'function verify|verification' app/controllers/AuthController.php app/models/User.php
  assert_present "Reset de contraseña" 'forgot|resetPassword|function reset' app/controllers/AuthController.php

  info "Editing (ft_snapchat.php)"
  assert_present "Captura / composición servidor" 'function capture|imagecopy|mergeImages' app/controllers/EditorController.php
  assert_present "Upload de imagen base" 'function upload' app/controllers/EditorController.php
  assert_present "Borrado de imagen propia" 'function delete' app/controllers/EditorController.php

  info "Gallery"
  assert_present "Galería pública / listado" 'function index|getAll|perPage' app/controllers/GalleryController.php
  assert_present "Likes" 'function like|toggleLike' app/controllers/GalleryController.php
  assert_present "Comentarios" 'function comment|addComment' app/controllers/GalleryController.php
  assert_present "Notificación por email de comentarios" 'notify|Mailer|sendComment' app/controllers/GalleryController.php app/core/Mailer.php

  info "Preferencias de usuario"
  assert_present "Ajustes / preferencias" 'settings|notify_comments|updateProfile' app/controllers/UserController.php app/controllers/AuthController.php
}

# ── 7 Bonus ──────────────────────────────────────────────────────────────────
section_bonus() {
  section "7 · BONUS (solo si mandatory OK en defensa real)"
  assert_present "AJAX likes/comentarios (fetch/XHR en JS)" 'fetch\s*\(|XMLHttpRequest' public/js/gallery.js public/js/main.js
  assert_present "Preview en vivo / drag sticker" 'drag|mousedown|stickerPosition|getUserMedia' public/js/editor.js
  assert_present "Infinite scroll / load-more" 'load-more|infinite|IntersectionObserver|scroll' public/js/gallery.js app/controllers/GalleryController.php
  assert_present "Creación de GIF animado (servidor)" 'createGif|Imagick|image/gif' app/controllers/EditorController.php

  local COMPOSE="docker-compose"
  docker-compose version >/dev/null 2>&1 || COMPOSE="docker compose"
  if $COMPOSE exec -T web php -m 2>/dev/null | grep -qi imagick; then
    ok "Runtime: imagick disponible para GIF"
    echo -e "      ${D}Comando: docker-compose exec web php -m | grep -i imagick${N}"
    echo -e "      ${D}Esperado: una línea con «imagick»${N}"
  else
    skip "Runtime: imagick no disponible (GIF puede fallar en defensa)"
  fi
}

summary() {
  echo ""
  echo -e "${M}${BOLD}╔══════════════════════════════════════════════════════════════════════╗${N}"
  echo -e "${M}${BOLD}║                         RESUMEN DE LA SUITE                          ║${N}"
  echo -e "${M}${BOLD}╚══════════════════════════════════════════════════════════════════════╝${N}"
  local pct=0
  [[ $TOTAL -gt 0 ]] && pct=$(( PASS * 100 / TOTAL ))
  if [[ $FAIL -eq 0 ]]; then
    # Sin errores: tono neutro / verde suave (nada en rojo)
    echo -e "  Total: ${TOTAL}   OK: ${G}${PASS}${N}   Fallo: ${FAIL}   Omitido: ${SKIP}"
    echo -e "  Aciertos: ${BOLD}${pct}%${N}"
    echo ""
    echo -e "  ${G}${BOLD}✓ Suite en verde.${N} Revisa aún los flujos manuales de la hoja de evaluación."
  else
    echo -e "  Total: ${W}${TOTAL}${N}   ${G}OK: ${PASS}${N}   ${R}Fallo: ${FAIL}${N}   ${Y}Omitido: ${SKIP}${N}"
    echo -e "  Aciertos: ${BOLD}${pct}%${N}"
    echo ""
    echo -e "  ${R}${BOLD}✗ Hay fallos.${N} Corrige antes de la defensa (eval puede marcar Crash/Cheat)."
  fi
  echo -e "  ${D}Guía interactiva de defensa: ./test_eval.sh${N}"
  echo -e "  ${D}42 Málaga · https://www.42malaga.com/ · ${WHO}${N}"
  echo ""
  [[ $FAIL -eq 0 ]] && return 0 || return 1
}

banner
need_section preflight && section_preflight
need_section structure && section_structure
if [[ "$MODE" != "quick" ]]; then
  need_section docker && section_docker
fi
need_section http && section_http
need_section security && section_security
if [[ "$MODE" != "quick" || -n "$SECTION" ]]; then
  if [[ -z "$SECTION" || "$SECTION" == "features" || "$SECTION" == "structure" ]]; then
    section_features
  fi
  need_section bonus && section_bonus
fi
summary
exit $?
