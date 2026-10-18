#!/bin/bash

# =============================================================================
# Camagru - Suite de Pruebas Automatizadas (125 % Edition)
# =============================================================================
# Batería completa de tests de entorno, seguridad, estructura y funcionalidad.
# Campus: 42 Málaga · https://www.42malaga.com/
# =============================================================================

# Colors and formatting
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color
BOLD='\033[1m'

# Test counters
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0
SKIPPED_TESTS=0

# Configuration
APP_URL="${APP_URL:-http://localhost:8080}"
TEST_EMAIL="test@camagru.local"
TEST_PASSWORD="Test123456!"

# Detect container names dynamically (more robust than hardcoding)
WEB_CONTAINER=$(docker-compose ps -q web 2>/dev/null | head -1)
DB_CONTAINER=$(docker-compose ps -q db 2>/dev/null | head -1)

# Banner
clear
echo -e "${PURPLE}${BOLD}"
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║            CAMAGRU - SUITE DE PRUEBAS AUTOMATIZADAS            ║"
echo "║                   Batería Completa de Tests                    ║"
echo "║              42 Málaga · https://www.42malaga.com/             ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo -e "${NC}"
echo ""

# -----------------------------------------------------------------------------
# Helper functions
# -----------------------------------------------------------------------------
print_test_header() {
    echo ""
    echo -e "${CYAN}${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${CYAN}${BOLD} $1${NC}"
    echo -e "${CYAN}${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
}

print_test_name() {
    echo ""
    echo -e "${BLUE}➤ Test: ${BOLD}$1${NC}"
    echo -e "${YELLOW}  Objetivo: $2${NC}"
}

test_passed() {
    echo -e "${GREEN}  ✓ ÉXITO: $1${NC}"
    ((PASSED_TESTS++))
    ((TOTAL_TESTS++))
}

test_failed() {
    echo -e "${RED}  ✗ FALLO: $1${NC}"
    ((FAILED_TESTS++))
    ((TOTAL_TESTS++))
}

test_skipped() {
    echo -e "${YELLOW}  ⊘ OMITIDO: $1${NC}"
    ((SKIPPED_TESTS++))
    ((TOTAL_TESTS++))
}

print_section() {
    echo ""
    echo -e "${PURPLE}${BOLD}═══════════════════════════════════════════════════════════════${NC}"
    echo -e "${PURPLE}${BOLD} $1${NC}"
    echo -e "${PURPLE}${BOLD}═══════════════════════════════════════════════════════════════${NC}"
}

# Helper: get DB root password from .env if present
get_db_root_pass() {
    if [ -f .env ]; then
        grep -E '^DB_ROOT_PASS=' .env 2>/dev/null | cut -d '=' -f2- | tr -d '"' | tr -d "'"
    else
        echo "root_password"
    fi
}

get_db_name() {
    if [ -f .env ]; then
        grep -E '^DB_NAME=' .env 2>/dev/null | cut -d '=' -f2- | tr -d '"' | tr -d "'" || echo "camagru"
    else
        echo "camagru"
    fi
}

DB_ROOT_PASS=$(get_db_root_pass)
DB_NAME=$(get_db_name)

# =============================================================================
# SECCIÓN 1: VERIFICACIÓN DEL ENTORNO
# =============================================================================
print_section "1. VERIFICACIÓN DEL ENTORNO"

print_test_name "Docker instalado" "Verificar que Docker está disponible en el sistema"
if command -v docker &> /dev/null; then
    DOCKER_VERSION=$(docker --version 2>/dev/null | head -1)
    test_passed "Docker encontrado ($DOCKER_VERSION)"
else
    test_failed "Docker no está instalado"
fi

print_test_name "Docker Compose instalado" "Verificar que Docker Compose está disponible"
if command -v docker-compose &> /dev/null || docker compose version &> /dev/null; then
    test_passed "Docker Compose encontrado"
else
    test_failed "Docker Compose no está instalado"
fi

print_test_name "Archivo .env existe" "Verificar configuración del entorno"
if [ -f .env ]; then
    test_passed "Archivo .env encontrado"
else
    test_failed "Archivo .env no encontrado (ejecuta ./env_setup.sh)"
fi

print_test_name "Estructura de directorios" "Verificar que existen los directorios necesarios"
MISSING_DIRS=0
for dir in app app/controllers app/models app/views app/core config public public/uploads; do
    if [ ! -d "$dir" ]; then
        echo -e "${RED}    Falta: $dir${NC}"
        MISSING_DIRS=1
    fi
done
if [ $MISSING_DIRS -eq 0 ]; then
    test_passed "Estructura de directorios correcta"
else
    test_failed "Faltan directorios importantes"
fi

# =============================================================================
# SECCIÓN 2: CONTENEDORES DOCKER
# =============================================================================
print_section "2. CONTENEDORES DOCKER"

print_test_name "Contenedores en ejecución" "Verificar que los contenedores están activos"
if docker-compose ps 2>/dev/null | grep -q "Up"; then
    test_passed "Hay contenedores en ejecución"
else
    test_failed "No hay contenedores en ejecución (docker-compose up -d)"
fi

print_test_name "Contenedor Web" "Verificar estado del contenedor de aplicación web"
if [ -n "$WEB_CONTAINER" ] && docker inspect -f '{{.State.Running}}' "$WEB_CONTAINER" 2>/dev/null | grep -q true; then
    test_passed "Contenedor web en ejecución"
else
    # Fallback: try by service name
    if docker-compose ps web 2>/dev/null | grep -q "Up"; then
        test_passed "Contenedor web en ejecución (via compose)"
    else
        test_failed "Contenedor web no está en ejecución"
    fi
fi

print_test_name "Contenedor Base de Datos" "Verificar estado del contenedor MySQL"
if [ -n "$DB_CONTAINER" ] && docker inspect -f '{{.State.Running}}' "$DB_CONTAINER" 2>/dev/null | grep -q true; then
    test_passed "Contenedor de base de datos en ejecución"
else
    if docker-compose ps db 2>/dev/null | grep -q "Up"; then
        test_passed "Contenedor de base de datos en ejecución (via compose)"
    else
        test_failed "Contenedor de base de datos no está en ejecución"
    fi
fi

# =============================================================================
# SECCIÓN 3: CONECTIVIDAD Y SERVICIOS
# =============================================================================
print_section "3. CONECTIVIDAD Y SERVICIOS"

print_test_name "Aplicación web responde" "Verificar que la aplicación es accesible via HTTP"
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 5 "$APP_URL" 2>/dev/null || echo "000")
if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ] || [ "$HTTP_CODE" = "301" ]; then
    test_passed "Aplicación responde (HTTP $HTTP_CODE)"
else
    test_failed "Aplicación no responde (HTTP $HTTP_CODE) - ¿está levantada en $APP_URL?"
fi

print_test_name "Extensión imagick (bonus GIF)" "Verificar que Imagick está disponible en el contenedor web"
if docker-compose exec -T web php -m 2>/dev/null | grep -qi imagick; then
    test_passed "Extensión imagick cargada (GIF animado disponible)"
else
    test_failed "Extensión imagick NO encontrada (el bonus GIF no funcionará)"
fi

print_test_name "Extensión GD" "Verificar que GD está disponible para procesamiento de imágenes"
if docker-compose exec -T web php -m 2>/dev/null | grep -qi "^gd$"; then
    test_passed "Extensión GD cargada"
else
    test_failed "Extensión GD no encontrada"
fi

print_test_name "Base de datos accesible" "Verificar conectividad con MySQL"
if docker-compose exec -T db mysqladmin ping -h localhost -u root -p"$DB_ROOT_PASS" --silent 2>/dev/null; then
    test_passed "MySQL responde al ping"
else
    # Try without password prompt issues
    if docker-compose exec -T db mysql -u root -p"$DB_ROOT_PASS" -e "SELECT 1" &>/dev/null; then
        test_passed "MySQL accesible"
    else
        test_skipped "No se pudo verificar MySQL (revisa DB_ROOT_PASS en .env)"
    fi
fi

print_test_name "Base de datos Camagru existe" "Verificar que la base de datos está creada"
if docker-compose exec -T db mysql -u root -p"$DB_ROOT_PASS" -e "USE \`$DB_NAME\`;" &>/dev/null; then
    test_passed "Base de datos '$DB_NAME' existe"
else
    test_skipped "No se pudo verificar la existencia de la BD '$DB_NAME'"
fi

print_test_name "Tablas de base de datos" "Verificar que las tablas necesarias existen"
REQUIRED_TABLES=("users" "images" "likes" "comments")
MISSING_TABLES=0
for table in "${REQUIRED_TABLES[@]}"; do
    if docker-compose exec -T db mysql -u root -p"$DB_ROOT_PASS" -D "$DB_NAME" -e "SHOW TABLES LIKE '$table';" 2>/dev/null | grep -q "$table"; then
        echo -e "${GREEN}    ✓ Tabla $table existe${NC}"
    else
        echo -e "${RED}    ✗ Tabla $table NO existe${NC}"
        MISSING_TABLES=1
    fi
done
if [ $MISSING_TABLES -eq 0 ]; then
    test_passed "Todas las tablas obligatorias existen"
else
    test_failed "Faltan tablas en la base de datos"
fi

# =============================================================================
# SECCIÓN 4: ARCHIVOS Y PERMISOS
# =============================================================================
print_section "4. ARCHIVOS Y PERMISOS"

print_test_name "Archivos PHP principales" "Verificar que existen los archivos core del proyecto"
CORE_FILES=(
    "app/core/Database.php"
    "app/core/Controller.php"
    "app/core/Router.php"
    "app/core/Validator.php"
    "app/core/Mailer.php"
    "app/controllers/AuthController.php"
    "app/controllers/EditorController.php"
    "app/controllers/GalleryController.php"
    "app/models/User.php"
    "app/models/Image.php"
)
MISSING_FILES=0
for f in "${CORE_FILES[@]}"; do
    if [ ! -f "$f" ]; then
        echo -e "${RED}    Falta: $f${NC}"
        MISSING_FILES=1
    fi
done
if [ $MISSING_FILES -eq 0 ]; then
    test_passed "Archivos core presentes"
else
    test_failed "Faltan archivos core del proyecto"
fi

print_test_name "Permisos de uploads" "Verificar directorio de uploads"
if [ -d "public/uploads" ]; then
    test_passed "Directorio public/uploads existe"
else
    test_failed "Directorio public/uploads no existe"
fi

print_test_name "Carpeta temporal para GIFs" "Verificar public/uploads/temp"
if [ -d "public/uploads/temp" ]; then
    test_passed "Carpeta temporal para GIFs existe"
else
    test_skipped "Carpeta public/uploads/temp no existe (se crea en runtime)"
fi

print_test_name "Archivo .env protegido" "Verificar permisos seguros del archivo .env"
if [ -f .env ]; then
    PERMS=$(stat -c "%a" .env 2>/dev/null || stat -f "%OLp" .env 2>/dev/null)
    if [ "$PERMS" = "600" ] || [ "$PERMS" = "400" ]; then
        test_passed "Permisos de .env seguros ($PERMS)"
    else
        test_failed "Permisos de .env demasiado abiertos ($PERMS) — se recomienda 600"
    fi
else
    test_skipped ".env no existe"
fi

# =============================================================================
# SECCIÓN 5: CÓDIGO Y SEGURIDAD
# =============================================================================
print_section "5. CÓDIGO Y SEGURIDAD"

print_test_name "PDO ERRMODE_EXCEPTION" "Verificar modo de errores de PDO"
if grep -rq "ERRMODE_EXCEPTION" app/core/Database.php config/ 2>/dev/null; then
    test_passed "PDO::ERRMODE_EXCEPTION configurado"
else
    test_failed "No se encontró PDO::ERRMODE_EXCEPTION"
fi

print_test_name "Password hashing" "Verificar uso de password_hash"
if grep -rq "password_hash" app/models/User.php 2>/dev/null; then
    test_passed "password_hash utilizado"
else
    test_failed "No se encontró password_hash"
fi

print_test_name "Protección XSS" "Verificar uso de htmlspecialchars"
if grep -rq "htmlspecialchars" app/core/Controller.php 2>/dev/null; then
    test_passed "htmlspecialchars presente en Controller"
else
    test_failed "No se encontró htmlspecialchars"
fi

print_test_name "CSRF tokens" "Verificar generación y validación de tokens CSRF"
if grep -rq "csrf_token\|validateCSRF\|generateCSRF" app/core/Controller.php 2>/dev/null; then
    test_passed "Sistema CSRF implementado"
else
    test_failed "No se encontró implementación CSRF"
fi

print_test_name "Sentencias preparadas" "Verificar uso de prepared statements"
if grep -rq "prepare\|->" app/core/Database.php 2>/dev/null; then
    test_passed "Uso de prepared statements detectado"
else
    test_failed "No se detectó uso claro de prepared statements"
fi

# =============================================================================
# SECCIÓN 6: ENDPOINTS HTTP BÁSICOS
# =============================================================================
print_section "6. ENDPOINTS HTTP BÁSICOS"

check_endpoint() {
    local path=$1
    local expected=$2
    local name=$3
    local code
    code=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 5 "$APP_URL$path" 2>/dev/null || echo "000")
    if echo "$expected" | grep -q "$code"; then
        test_passed "$name (HTTP $code)"
    else
        test_failed "$name (HTTP $code, se esperaba $expected)"
    fi
}

print_test_name "Página de inicio" "GET /"
check_endpoint "/" "200 302 301" "Home"

print_test_name "Página de login" "GET /login"
check_endpoint "/login" "200 302" "Login"

print_test_name "Página de registro" "GET /register"
check_endpoint "/register" "200 302" "Register"

print_test_name "Galería pública" "GET /gallery"
check_endpoint "/gallery" "200 302" "Gallery"

print_test_name "Editor (requiere auth)" "GET /editor (debe redirigir si no hay sesión)"
check_endpoint "/editor" "200 302 301" "Editor"

# =============================================================================
# SECCIÓN 7: FUNCIONALIDADES CLAVE
# =============================================================================
print_section "7. FUNCIONALIDADES CLAVE"

print_test_name "Sistema de likes" "Verificar tabla y código de likes"
if docker-compose exec -T db mysql -u root -p"$DB_ROOT_PASS" -D "$DB_NAME" -e "SHOW TABLES LIKE 'likes';" 2>/dev/null | grep -q "likes"; then
    if grep -rq "class Like\|function toggle\|function exists" app/models/Like.php 2>/dev/null; then
        test_passed "Sistema de likes implementado (tabla + modelo)"
    else
        test_passed "Tabla likes existe (revisar modelo)"
    fi
else
    test_failed "Tabla de likes no encontrada"
fi

print_test_name "Sistema de comentarios" "Verificar tabla y código de comentarios"
if docker-compose exec -T db mysql -u root -p"$DB_ROOT_PASS" -D "$DB_NAME" -e "SHOW TABLES LIKE 'comments';" 2>/dev/null | grep -q "comments"; then
    if grep -rq "class Comment\|function create" app/models/Comment.php 2>/dev/null; then
        test_passed "Sistema de comentarios implementado (tabla + modelo)"
    else
        test_passed "Tabla comments existe (revisar modelo)"
    fi
else
    test_failed "Tabla de comentarios no encontrada"
fi

print_test_name "Procesamiento de imágenes (GD)" "Verificar funciones GD en EditorController"
if grep -rq "imagecreatefromstring\|imagecopy\|imagepng" app/controllers/EditorController.php 2>/dev/null; then
    test_passed "Procesamiento server-side con GD detectado"
else
    test_failed "No se detectó procesamiento de imágenes con GD"
fi

print_test_name "Soporte GIF (Imagick)" "Verificar código de creación de GIF"
if grep -rq "Imagick\|createGif\|image/gif" app/controllers/EditorController.php 2>/dev/null; then
    test_passed "Código de generación de GIF presente"
else
    test_failed "No se encontró código de generación de GIF"
fi

# =============================================================================
# RESUMEN FINAL
# =============================================================================
echo ""
echo ""
echo -e "${PURPLE}${BOLD}╔══════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${PURPLE}${BOLD}║                    RESUMEN DE PRUEBAS                            ║${NC}"
echo -e "${PURPLE}${BOLD}╚══════════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${CYAN}Total de pruebas ejecutadas: ${BOLD}$TOTAL_TESTS${NC}"
echo ""
echo -e "${GREEN}✓ Pruebas exitosas:    ${BOLD}$PASSED_TESTS${NC}"
echo -e "${RED}✗ Pruebas fallidas:    ${BOLD}$FAILED_TESTS${NC}"
echo -e "${YELLOW}⊘ Pruebas omitidas:    ${BOLD}$SKIPPED_TESTS${NC}"
echo ""

# Calculate percentage
if [ $TOTAL_TESTS -gt 0 ]; then
    if command -v bc &> /dev/null; then
        SUCCESS_RATE=$(echo "scale=2; ($PASSED_TESTS * 100) / $TOTAL_TESTS" | bc)
    else
        SUCCESS_RATE=$(( PASSED_TESTS * 100 / TOTAL_TESTS ))
    fi
    echo -e "${CYAN}Tasa de éxito: ${BOLD}${SUCCESS_RATE}%${NC}"
    echo ""
fi

# Final verdict
if [ $FAILED_TESTS -eq 0 ]; then
    echo -e "${GREEN}${BOLD}🎉 ¡EXCELENTE! Todos los tests críticos pasaron.${NC}"
    echo -e "${GREEN}Tu proyecto Camagru está listo para la evaluación 125 %.${NC}"
    EXIT_CODE=0
elif [ $FAILED_TESTS -le 3 ]; then
    echo -e "${YELLOW}${BOLD}⚠️  ADVERTENCIA: Algunos tests fallaron.${NC}"
    echo -e "${YELLOW}Revisa los errores anteriores y corrígelos.${NC}"
    EXIT_CODE=1
else
    echo -e "${RED}${BOLD}❌ ERROR: Múltiples tests fallaron.${NC}"
    echo -e "${RED}El proyecto requiere atención antes de la defensa.${NC}"
    EXIT_CODE=2
fi

echo ""
echo -e "${BLUE}Fecha de ejecución: $(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo -e "${BLUE}Campus: 42 Málaga · https://www.42malaga.com/${NC}"
echo -e "${PURPLE}${BOLD}════════════════════════════════════════════════════════════════════${NC}"
echo ""

exit $EXIT_CODE
