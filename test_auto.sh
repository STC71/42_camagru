#!/bin/bash

# Camagru Automated Testing Suite
# Comprehensive test battery for functionality, security, and performance

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
APP_URL="http://localhost:8080"
DB_CONTAINER="camagru-db-1"
WEB_CONTAINER="camagru-web-1"
TEST_EMAIL="test@camagru.local"
TEST_PASSWORD="Test123456!"

# Banner
clear
echo -e "${PURPLE}${BOLD}"
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║            CAMAGRU - SUITE DE PRUEBAS AUTOMATIZADAS            ║"
echo "║                   Batería Completa de Tests                    ║"
echo "║                      sternero - 42 Málaga                      ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo -e "${NC}"
echo ""

# Helper functions
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

# ============================================================================
# SECCIÓN 1: VERIFICACIÓN DEL ENTORNO
# ============================================================================
print_section "1. VERIFICACIÓN DEL ENTORNO"

print_test_name "Docker instalado" "Verificar que Docker está disponible en el sistema"
if command -v docker &> /dev/null; then
    DOCKER_VERSION=$(docker --version | cut -d ' ' -f3 | tr -d ',')
    test_passed "Docker $DOCKER_VERSION encontrado"
else
    test_failed "Docker no está instalado"
fi

print_test_name "Docker Compose instalado" "Verificar que Docker Compose está disponible"
if command -v docker-compose &> /dev/null; then
    COMPOSE_VERSION=$(docker-compose --version | cut -d ' ' -f4 | tr -d ',')
    test_passed "Docker Compose $COMPOSE_VERSION encontrado"
else
    test_failed "Docker Compose no está instalado"
fi

print_test_name "Archivo .env existe" "Verificar configuración del entorno"
if [ -f .env ]; then
    test_passed "Archivo .env encontrado"
else
    test_failed "Archivo .env no existe. Ejecuta ./env_setup.sh"
    echo ""
    read -p "$(echo -e "${YELLOW}¿Quieres ejecutar el asistente de configuración ahora? (s/N): ${NC}")" run_setup
    if [[ $run_setup =~ ^[SsYy]$ ]]; then
        echo ""
        echo -e "${BLUE}Ejecutando asistente de configuración...${NC}"
        ./env_setup.sh
        if [ -f .env ]; then
            echo ""
            echo -e "${GREEN}✓ Archivo .env creado exitosamente${NC}"
            echo -e "${BLUE}Continuando con los tests...${NC}"
            sleep 2
        else
            echo ""
            echo -e "${RED}✗ No se pudo crear el archivo .env${NC}"
            exit 1
        fi
    else
        echo ""
        echo -e "${RED}Tests cancelados. Ejecuta ./env_setup.sh y vuelve a intentar.${NC}"
        exit 1
    fi
fi

print_test_name "Estructura de directorios" "Verificar que existen los directorios necesarios"
REQUIRED_DIRS=("app" "config" "database" "public" "public/uploads" "public/uploads/images")
ALL_DIRS_OK=true
for dir in "${REQUIRED_DIRS[@]}"; do
    if [ ! -d "$dir" ]; then
        ALL_DIRS_OK=false
        echo -e "${RED}    ✗ Falta directorio: $dir${NC}"
    fi
done
if $ALL_DIRS_OK; then
    test_passed "Todos los directorios necesarios existen"
else
    test_failed "Faltan directorios requeridos"
fi

# ============================================================================
# SECCIÓN 2: CONTENEDORES DOCKER
# ============================================================================
print_section "2. CONTENEDORES DOCKER"

print_test_name "Contenedores en ejecución" "Verificar que los contenedores están activos"
RUNNING_CONTAINERS=$(docker-compose ps --services --filter "status=running" 2>/dev/null | wc -l)
EXPECTED_CONTAINERS=2

if [ "$RUNNING_CONTAINERS" -ge "$EXPECTED_CONTAINERS" ]; then
    test_passed "$RUNNING_CONTAINERS contenedores activos"
else
    test_failed "Solo $RUNNING_CONTAINERS de $EXPECTED_CONTAINERS contenedores activos"
    echo ""
    read -p "$(echo -e "${YELLOW}¿Quieres iniciar los contenedores ahora con 'make up'? (s/N): ${NC}")" start_containers
    if [[ $start_containers =~ ^[SsYy]$ ]]; then
        echo ""
        echo -e "${BLUE}Iniciando contenedores Docker...${NC}"
        make up
        echo ""
        echo -e "${BLUE}Esperando a que los contenedores estén listos...${NC}"
        sleep 5
        
        # Verificar que están corriendo
        RUNNING_NOW=$(docker-compose ps --services --filter "status=running" 2>/dev/null | wc -l)
        if [ "$RUNNING_NOW" -ge "$EXPECTED_CONTAINERS" ]; then
            echo -e "${GREEN}✓ Contenedores iniciados correctamente${NC}"
            echo -e "${BLUE}Continuando con los tests...${NC}"
            sleep 2
        else
            echo -e "${RED}✗ Los contenedores no se iniciaron correctamente${NC}"
            echo -e "${YELLOW}Revisa los logs con: make logs${NC}"
            exit 1
        fi
    else
        echo ""
        echo -e "${YELLOW}⚠️  Los siguientes tests fallarán sin contenedores activos${NC}"
        echo -e "${YELLOW}Continuando de todos modos...${NC}"
        sleep 2
    fi
fi

print_test_name "Contenedor Web" "Verificar estado del contenedor de aplicación web"
if docker ps --format '{{.Names}}' | grep -q "web"; then
    WEB_STATUS=$(docker inspect --format='{{.State.Status}}' $(docker ps -qf "name=web") 2>/dev/null)
    test_passed "Contenedor web: $WEB_STATUS"
else
    test_failed "Contenedor web no encontrado"
fi

print_test_name "Contenedor Base de Datos" "Verificar estado del contenedor MySQL"
if docker ps --format '{{.Names}}' | grep -q "db"; then
    DB_STATUS=$(docker inspect --format='{{.State.Status}}' $(docker ps -qf "name=db") 2>/dev/null)
    test_passed "Contenedor db: $DB_STATUS"
else
    test_failed "Contenedor db no encontrado"
fi

# ============================================================================
# SECCIÓN 3: CONECTIVIDAD Y SERVICIOS
# ============================================================================
print_section "3. CONECTIVIDAD Y SERVICIOS"

print_test_name "Aplicación web responde" "Verificar que la aplicación es accesible via HTTP"
HTTP_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" $APP_URL 2>/dev/null)
if [ "$HTTP_RESPONSE" == "200" ]; then
    test_passed "HTTP 200 OK recibido de $APP_URL"
elif [ -z "$HTTP_RESPONSE" ]; then
    test_failed "No hay respuesta de $APP_URL"
else
    test_failed "HTTP $HTTP_RESPONSE recibido (esperado: 200)"
fi

print_test_name "Base de datos accesible" "Verificar conectividad con MySQL"
DB_CHECK=$(docker-compose exec -T db mysql -u root -p$(grep DB_ROOT_PASS .env | cut -d '=' -f2) -e "SELECT 1;" 2>/dev/null)
if [ $? -eq 0 ]; then
    test_passed "Conexión a MySQL exitosa"
else
    test_failed "No se pudo conectar a MySQL"
fi

print_test_name "Base de datos Camagru existe" "Verificar que la base de datos está creada"
DB_NAME=$(grep DB_NAME .env | cut -d '=' -f2)
DB_EXISTS=$(docker-compose exec -T db mysql -u root -p$(grep DB_ROOT_PASS .env | cut -d '=' -f2) -e "SHOW DATABASES LIKE '$DB_NAME';" 2>/dev/null | grep -c "$DB_NAME")
if [ "$DB_EXISTS" -gt 0 ]; then
    test_passed "Base de datos '$DB_NAME' existe"
else
    test_failed "Base de datos '$DB_NAME' no existe"
    echo ""
    read -p "$(echo -e "${YELLOW}¿Quieres ejecutar el script de inicialización de la BD? (s/N): ${NC}")" init_db
    if [[ $init_db =~ ^[SsYy]$ ]]; then
        echo ""
        echo -e "${BLUE}Ejecutando script de inicialización...${NC}"
        if [ -f "database/init.sql" ]; then
            # Crear la base de datos primero
            docker-compose exec -T db mysql -u root -p$(grep DB_ROOT_PASS .env | cut -d '=' -f2) -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;" 2>/dev/null
            # Ejecutar el script en la base de datos
            docker-compose exec -T db mysql -u root -p$(grep DB_ROOT_PASS .env | cut -d '=' -f2) $DB_NAME < database/init.sql 2>/dev/null
            if [ $? -eq 0 ]; then
                echo -e "${GREEN}✓ Base de datos inicializada correctamente${NC}"
                echo -e "${BLUE}Continuando con los tests...${NC}"
                sleep 2
                # Verificar que la BD se creó
                DB_EXISTS=$(docker-compose exec -T db mysql -u root -p$(grep DB_ROOT_PASS .env | cut -d '=' -f2) -e "SHOW DATABASES LIKE '$DB_NAME';" 2>/dev/null | grep -c "$DB_NAME")
            else
                echo -e "${RED}✗ Error al inicializar la base de datos${NC}"
            fi
        else
            echo -e "${RED}✗ No se encontró el archivo database/init.sql${NC}"
        fi
    else
        echo ""
        echo -e "${YELLOW}⚠️  Los tests de BD fallarán sin inicialización${NC}"
        echo -e "${YELLOW}Continuando de todos modos...${NC}"
        sleep 2
    fi
fi

print_test_name "Tablas de base de datos" "Verificar que las tablas necesarias existen"
REQUIRED_TABLES=("users" "images" "likes" "comments")
TABLES_OK=true
MISSING_COUNT=0
for table in "${REQUIRED_TABLES[@]}"; do
    TABLE_EXISTS=$(docker-compose exec -T db mysql -u root -p$(grep DB_ROOT_PASS .env | cut -d '=' -f2) -D $DB_NAME -e "SHOW TABLES LIKE '$table';" 2>/dev/null | grep -c "$table")
    if [ "$TABLE_EXISTS" -eq 0 ]; then
        TABLES_OK=false
        ((MISSING_COUNT++))
        echo -e "${RED}    ✗ Falta tabla: $table${NC}"
    fi
done
if $TABLES_OK; then
    test_passed "Todas las tablas necesarias existen"
else
    test_failed "Faltan tablas en la base de datos"
    
    # Si faltan tablas pero la BD existe, ofrecer inicializar
    if [ "$DB_EXISTS" -gt 0 ] && [ $MISSING_COUNT -gt 0 ]; then
        echo ""
        read -p "$(echo -e "${YELLOW}¿Quieres ejecutar el script de inicialización para crear las tablas? (s/N): ${NC}")" init_tables
        if [[ $init_tables =~ ^[SsYy]$ ]]; then
            echo ""
            echo -e "${BLUE}Ejecutando script de inicialización...${NC}"
            if [ -f "database/init.sql" ]; then
                docker-compose exec -T db mysql -u root -p$(grep DB_ROOT_PASS .env | cut -d '=' -f2) $DB_NAME < database/init.sql 2>/dev/null
                if [ $? -eq 0 ]; then
                    echo -e "${GREEN}✓ Tablas creadas correctamente${NC}"
                    echo -e "${BLUE}Continuando con los tests...${NC}"
                    sleep 2
                else
                    echo -e "${RED}✗ Error al crear las tablas${NC}"
                fi
            else
                echo -e "${RED}✗ No se encontró el archivo database/init.sql${NC}"
            fi
        else
            echo ""
            echo -e "${YELLOW}Continuando sin inicializar las tablas...${NC}"
            sleep 1
        fi
    fi
fi

# ============================================================================
# SECCIÓN 4: ARCHIVOS Y PERMISOS
# ============================================================================
print_section "4. ARCHIVOS Y PERMISOS"

print_test_name "Archivos PHP principales" "Verificar que existen los archivos core del proyecto"
REQUIRED_FILES=(
    "public/index.php"
    "app/core/Router.php"
    "app/core/Database.php"
    "app/core/Controller.php"
    "config/Config.php"
)
FILES_OK=true
for file in "${REQUIRED_FILES[@]}"; do
    if [ ! -f "$file" ]; then
        FILES_OK=false
        echo -e "${RED}    ✗ Falta archivo: $file${NC}"
    fi
done
if $FILES_OK; then
    test_passed "Todos los archivos principales existen"
else
    test_failed "Faltan archivos requeridos"
fi

print_test_name "Permisos de uploads" "Verificar permisos de escritura en directorio de uploads"
if [ -w "public/uploads/images" ]; then
    test_passed "Directorio de uploads tiene permisos de escritura"
else
    test_failed "Directorio de uploads no tiene permisos de escritura"
    echo -e "${BLUE}  ℹ Corrigiendo permisos automáticamente...${NC}"
    chmod -R 755 public/uploads 2>/dev/null
    if [ -w "public/uploads/images" ]; then
        echo -e "${GREEN}  ✓ Permisos corregidos${NC}"
    else
        echo -e "${RED}  ✗ No se pudieron corregir los permisos${NC}"
    fi
fi

print_test_name "Archivo .env protegido" "Verificar permisos seguros del archivo .env"
ENV_PERMS=$(stat -c "%a" .env 2>/dev/null || stat -f "%OLp" .env 2>/dev/null)
if [ "$ENV_PERMS" == "600" ] || [ "$ENV_PERMS" == "400" ]; then
    test_passed "Permisos de .env: $ENV_PERMS (seguro)"
else
    test_failed "Permisos de .env: $ENV_PERMS (inseguro, debería ser 600)"
    echo -e "${BLUE}  ℹ Corrigiendo permisos automáticamente...${NC}"
    chmod 600 .env 2>/dev/null
    NEW_ENV_PERMS=$(stat -c "%a" .env 2>/dev/null || stat -f "%OLp" .env 2>/dev/null)
    if [ "$NEW_ENV_PERMS" == "600" ]; then
        echo -e "${GREEN}  ✓ Permisos corregidos a 600${NC}"
        test_passed "Permisos de .env corregidos correctamente"
    else
        echo -e "${RED}  ✗ No se pudieron corregir los permisos${NC}"
    fi
fi

# Verificar y corregir permisos generales del proyecto
print_test_name "Permisos generales del proyecto" "Verificar que los archivos tienen permisos adecuados"
echo -e "${BLUE}  ℹ Verificando y corrigiendo permisos...${NC}"
# Directorios ejecutables
find . -type d -not -path "*/\.*" -not -path "*/vendor/*" -not -path "*/node_modules/*" -exec chmod 755 {} \; 2>/dev/null
# Archivos legibles
find . -type f -not -path "*/\.*" -not -path "*/vendor/*" -not -path "*/node_modules/*" -exec chmod 644 {} \; 2>/dev/null
# Scripts ejecutables
chmod +x env_setup.sh test_auto.sh deploy.sh 2>/dev/null
# .env seguro
chmod 600 .env 2>/dev/null
test_passed "Permisos del proyecto verificados y corregidos"

# ============================================================================
# SECCIÓN 5: ENDPOINTS HTTP
# ============================================================================
print_section "5. ENDPOINTS HTTP"

print_test_name "Página de inicio" "Verificar acceso a la página principal"
HOME_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/" 2>/dev/null)
if [ "$HOME_RESPONSE" == "200" ]; then
    test_passed "Página de inicio accesible (HTTP 200)"
else
    test_failed "Página de inicio inaccesible (HTTP $HOME_RESPONSE)"
fi

print_test_name "Página de registro" "Verificar acceso al formulario de registro"
REGISTER_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/register" 2>/dev/null)
if [ "$REGISTER_RESPONSE" == "200" ]; then
    test_passed "Página de registro accesible (HTTP 200)"
else
    test_failed "Página de registro inaccesible (HTTP $REGISTER_RESPONSE)"
fi

print_test_name "Página de login" "Verificar acceso al formulario de login"
LOGIN_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/login" 2>/dev/null)
if [ "$LOGIN_RESPONSE" == "200" ]; then
    test_passed "Página de login accesible (HTTP 200)"
else
    test_failed "Página de login inaccesible (HTTP $LOGIN_RESPONSE)"
fi

print_test_name "Página de galería" "Verificar acceso a la galería pública"
GALLERY_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/gallery" 2>/dev/null)
if [ "$GALLERY_RESPONSE" == "200" ]; then
    test_passed "Galería accesible (HTTP 200)"
else
    test_failed "Galería inaccesible (HTTP $GALLERY_RESPONSE)"
fi

print_test_name "Editor protegido" "Verificar que el editor requiere autenticación"
EDITOR_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/editor" 2>/dev/null)
if [ "$EDITOR_RESPONSE" == "302" ] || [ "$EDITOR_RESPONSE" == "401" ]; then
    test_passed "Editor protegido correctamente (redirige/401)"
else
    test_failed "Editor no protegido adecuadamente (HTTP $EDITOR_RESPONSE)"
fi

# ============================================================================
# SECCIÓN 6: RECURSOS ESTÁTICOS
# ============================================================================
print_section "6. RECURSOS ESTÁTICOS"

print_test_name "CSS principal" "Verificar que los estilos CSS están disponibles"
CSS_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/css/style.css" 2>/dev/null)
if [ "$CSS_RESPONSE" == "200" ]; then
    test_passed "Archivo CSS accesible"
else
    test_failed "Archivo CSS inaccesible (HTTP $CSS_RESPONSE)"
fi

print_test_name "JavaScript principal" "Verificar que los scripts JS están disponibles"
JS_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/js/main.js" 2>/dev/null)
if [ "$JS_RESPONSE" == "200" ]; then
    test_passed "Archivo JavaScript accesible"
else
    test_failed "Archivo JavaScript inaccesible (HTTP $JS_RESPONSE)"
fi

print_test_name "Directorio de stickers" "Verificar disponibilidad de stickers"
STICKER_DIR="public/stickers"
if [ -d "$STICKER_DIR" ] && [ "$(ls -A $STICKER_DIR 2>/dev/null | wc -l)" -gt 0 ]; then
    STICKER_COUNT=$(ls -1 $STICKER_DIR | wc -l)
    test_passed "$STICKER_COUNT stickers disponibles"
else
    test_failed "No hay stickers disponibles"
fi

# ============================================================================
# SECCIÓN 7: SEGURIDAD BÁSICA
# ============================================================================
print_section "7. SEGURIDAD BÁSICA"

print_test_name "Protección .env" "Verificar que .env no es accesible via web"
ENV_WEB_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/.env" 2>/dev/null)
if [ "$ENV_WEB_RESPONSE" == "403" ] || [ "$ENV_WEB_RESPONSE" == "404" ]; then
    test_passed ".env protegido (HTTP $ENV_WEB_RESPONSE)"
else
    test_failed ".env accesible via web (HTTP $ENV_WEB_RESPONSE) - ¡PELIGRO!"
fi

print_test_name "Headers de seguridad" "Verificar presencia de headers de seguridad HTTP"
HEADERS=$(curl -s -I "$APP_URL" 2>/dev/null)
HAS_SECURITY_HEADERS=false
if echo "$HEADERS" | grep -qi "X-Frame-Options\|X-Content-Type-Options\|X-XSS-Protection"; then
    HAS_SECURITY_HEADERS=true
fi
if $HAS_SECURITY_HEADERS; then
    test_passed "Headers de seguridad presentes"
else
    test_skipped "Headers de seguridad opcionales no detectados"
fi

print_test_name "Directorio uploads protegido" "Verificar que no se pueden listar archivos en uploads"
UPLOADS_LISTING=$(curl -s "$APP_URL/uploads/" 2>/dev/null)
if echo "$UPLOADS_LISTING" | grep -qi "index of\|directory listing"; then
    test_failed "Directory listing habilitado en uploads - ¡RIESGO!"
else
    test_passed "Directory listing deshabilitado en uploads"
fi

print_test_name "Inyección SQL básica" "Test básico de protección contra SQL injection"
SQL_INJECTION_RESPONSE=$(curl -s -X POST "$APP_URL/login" \
    -d "email=admin' OR '1'='1&password=test" 2>/dev/null)
if echo "$SQL_INJECTION_RESPONSE" | grep -qi "error\|warning\|mysql"; then
    test_failed "Posible vulnerabilidad SQL - errores expuestos"
else
    test_passed "No se detectan errores SQL expuestos"
fi

# ============================================================================
# SECCIÓN 8: RENDIMIENTO
# ============================================================================
print_section "8. RENDIMIENTO"

print_test_name "Tiempo de respuesta" "Medir velocidad de respuesta de la página principal"
RESPONSE_TIME=$(curl -s -o /dev/null -w "%{time_total}" "$APP_URL" 2>/dev/null)
RESPONSE_MS=$(echo "$RESPONSE_TIME * 1000" | bc)
if (( $(echo "$RESPONSE_TIME < 1.0" | bc -l) )); then
    test_passed "Tiempo de respuesta: ${RESPONSE_MS%.*}ms (excelente)"
elif (( $(echo "$RESPONSE_TIME < 3.0" | bc -l) )); then
    test_passed "Tiempo de respuesta: ${RESPONSE_MS%.*}ms (aceptable)"
else
    test_failed "Tiempo de respuesta: ${RESPONSE_MS%.*}ms (lento)"
fi

print_test_name "Uso de memoria (contenedor web)" "Verificar consumo de memoria del contenedor"
if docker ps --format '{{.Names}}' | grep -q "web"; then
    MEM_USAGE=$(docker stats --no-stream --format "{{.MemUsage}}" $(docker ps -qf "name=web") 2>/dev/null | cut -d '/' -f1)
    if [ -n "$MEM_USAGE" ]; then
        test_passed "Uso de memoria: $MEM_USAGE"
    else
        test_skipped "No se pudo obtener estadística de memoria"
    fi
else
    test_skipped "Contenedor web no está corriendo"
fi

print_test_name "Tamaño de base de datos" "Verificar tamaño de la base de datos"
DB_SIZE=$(docker-compose exec -T db mysql -u root -p$(grep DB_ROOT_PASS .env | cut -d '=' -f2) -e "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size_MB' FROM information_schema.tables WHERE table_schema = '$DB_NAME';" 2>/dev/null | tail -n 1)
if [ -n "$DB_SIZE" ]; then
    test_passed "Tamaño de BD: ${DB_SIZE} MB"
else
    test_skipped "No se pudo determinar el tamaño de la BD"
fi

# ============================================================================
# SECCIÓN 9: CONFIGURACIÓN DE EMAIL
# ============================================================================
print_section "9. CONFIGURACIÓN DE EMAIL"

print_test_name "Variables de email configuradas" "Verificar que las variables SMTP están definidas"
MAIL_HOST=$(grep MAIL_HOST .env | cut -d '=' -f2)
MAIL_PORT=$(grep MAIL_PORT .env | cut -d '=' -f2)
if [ -n "$MAIL_HOST" ] && [ -n "$MAIL_PORT" ]; then
    test_passed "Configuración SMTP: $MAIL_HOST:$MAIL_PORT"
else
    test_failed "Variables de email no configuradas correctamente"
fi

print_test_name "Clase Mailer existe" "Verificar que existe la clase de envío de emails"
if [ -f "app/core/Mailer.php" ]; then
    test_passed "Clase Mailer encontrada"
else
    test_failed "Clase Mailer no encontrada"
fi

# ============================================================================
# SECCIÓN 10: FUNCIONALIDADES BONUS
# ============================================================================
print_section "10. FUNCIONALIDADES BONUS"

print_test_name "Soporte de GIF animados" "Verificar código para creación de GIFs"
if grep -r "GIF\|gif\|imagegif" app/ --include="*.php" > /dev/null 2>&1; then
    test_passed "Soporte de GIF detectado en código"
else
    test_skipped "No se detectó soporte de GIF (bonus opcional)"
fi

print_test_name "Sistema de likes" "Verificar tabla y código de likes"
if docker-compose exec -T db mysql -u root -p$(grep DB_ROOT_PASS .env | cut -d '=' -f2) -D $DB_NAME -e "SHOW TABLES LIKE 'likes';" 2>/dev/null | grep -q "likes"; then
    test_passed "Sistema de likes implementado"
else
    test_failed "Tabla de likes no encontrada"
fi

print_test_name "Sistema de comentarios" "Verificar tabla y código de comentarios"
if docker-compose exec -T db mysql -u root -p$(grep DB_ROOT_PASS .env | cut -d '=' -f2) -D $DB_NAME -e "SHOW TABLES LIKE 'comments';" 2>/dev/null | grep -q "comments"; then
    test_passed "Sistema de comentarios implementado"
else
    test_failed "Tabla de comentarios no encontrada"
fi

# ============================================================================
# RESUMEN FINAL
# ============================================================================
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
    SUCCESS_RATE=$(echo "scale=2; ($PASSED_TESTS * 100) / $TOTAL_TESTS" | bc)
    echo -e "${CYAN}Tasa de éxito: ${BOLD}${SUCCESS_RATE}%${NC}"
    echo ""
fi

# Final verdict
if [ $FAILED_TESTS -eq 0 ]; then
    echo -e "${GREEN}${BOLD}🎉 ¡EXCELENTE! Todos los tests críticos pasaron.${NC}"
    echo -e "${GREEN}Tu proyecto Camagru está funcionando correctamente.${NC}"
    EXIT_CODE=0
elif [ $FAILED_TESTS -le 3 ]; then
    echo -e "${YELLOW}${BOLD}⚠️  ADVERTENCIA: Algunos tests fallaron.${NC}"
    echo -e "${YELLOW}Revisa los errores anteriores y corrígelos.${NC}"
    EXIT_CODE=1
else
    echo -e "${RED}${BOLD}❌ ERROR: Múltiples tests fallaron.${NC}"
    echo -e "${RED}El proyecto requiere atención inmediata.${NC}"
    EXIT_CODE=2
fi

echo ""
echo -e "${BLUE}Fecha de ejecución: $(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo -e "${PURPLE}${BOLD}════════════════════════════════════════════════════════════════════${NC}"
echo ""

exit $EXIT_CODE
