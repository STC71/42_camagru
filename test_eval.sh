#!/bin/bash

# ============================================================================
#  📸 CAMAGRU - TEST DE EVALUACIÓN INTERACTIVA (125% EDITION) 📸
# ============================================================================
#  Test guiado paso a paso para la defensa de Camagru en 42
#  Campus: 42 Málaga - https://www.42malaga.com/
# ============================================================================

# ==========================================
# COLORES Y FORMATO
# ==========================================
C_R='\033[0;31m'
C_G='\033[0;32m'
C_Y='\033[0;33m'
C_B='\033[0;34m'
C_C='\033[0;36m'
C_M='\033[0;35m'
C_W='\033[1;37m'
C_DF='\033[0m'
BOLD='\033[1m'

# ==========================================
# FUNCIONES AUXILIARES
# ==========================================
pause() {
    echo -e "\n${C_C}>> Pulsa [ENTER] para avanzar al siguiente paso de la evaluación...${C_DF}"
    read -r
}

print_header() {
    clear
    echo -e "${C_B}╭───────────────────────────────────────────────────────────────────╮${C_DF}"
    echo -e "${C_B}│${C_W}${BOLD}                  EVALUACIÓN DE CAMAGRU                    ${C_DF}${C_B}│${C_DF}"
    echo -e "${C_B}│${C_DF}                    42 Málaga · 125 % target               ${C_B}│${C_DF}"
    echo -e "${C_B}╰───────────────────────────────────────────────────────────────────╯${C_DF}\n"
}

find_code() {
    local label=$1
    local search=$2
    shift 2
    local files="$@"

    echo -e "${C_Y}🔍 CÓDIGO:${C_DF} ${label}"
    for file in $files; do
        if [ -f "$file" ]; then
            lines=$(grep -n -E "$search" "$file" 2>/dev/null | awk -F: '{print $1}' | paste -sd, -)
            if [ ! -z "$lines" ]; then
                echo -e "   ↳ ${C_C}${file}${C_DF} [Líneas: ${C_G}${lines}${C_DF}]"
            fi
        fi
    done
}

# ==========================================
# INICIO DE EVALUACIÓN
# ==========================================
print_header
echo -e "${C_W}Este script interactivo te guiará paso a paso por todos los requisitos${C_DF}"
echo -e "${C_W}de Camagru, siguiendo la hoja oficial de evaluación de 42.${C_DF}"
echo -e "${C_W}Campus de referencia: ${C_G}https://www.42malaga.com/${C_DF}"
pause

# --- PASO 1: VERIFICACIONES PREVIAS ---
print_header
echo -e "${C_B}▶ PASO 1: PREFLIGHT CHECKS (OBLIGATORIOS)${C_DF}"
echo -e "Los evaluadores comprobarán que tienes los ficheros obligatorios:\n"
echo -e " - ${C_G}index.php${C_DF} (en public/ o raíz según configuración)"
echo -e " - ${C_G}config/database.php${C_DF}"
echo -e " - ${C_G}config/setup.php${C_DF}\n"
find_code "Archivos Mandatorios" "require_once|PDO" "public/index.php" "index.php" "config/database.php" "config/setup.php"
echo -e "\nTambién verifican la instanciación de PDO con PDO::ERRMODE_EXCEPTION."
find_code "Modo de Errores PDO" "ERRMODE_EXCEPTION" "app/core/Database.php" "config/setup.php"
echo -e "\nComprueba que ImageMagick / imagick está disponible (bonus GIF):"
echo -e "  ${C_M}docker-compose exec web php -m | grep imagick${C_DF}"
pause

# --- PASO 2: IGNICIÓN (SERVER START) ---
print_header
echo -e "${C_B}▶ PASO 2: 3..2..1.. IGNITION${C_DF}"
echo -e "Comando a ejecutar por el evaluador o ti mismo:"
echo -e "  ${C_M}docker-compose up --build -d${C_DF}   o   ${C_M}make up${C_DF}"
echo -e "\nAbre el navegador web en: ${C_G}http://localhost:8080${C_DF}"
echo -e "Comprueba que la consola no escupe ningún warning de backend ni front."
pause

# --- PASO 3: USER CREATION ---
print_header
echo -e "${C_B}▶ PASO 3: USER CREATION (REGISTRO)${C_DF}"
echo -e "¿Qué probar en la web?"
echo -e " 1. Ir a Registro y rellenar con nombre, email y contraseña."
echo -e " 2. Comprobar que una contraseña simple ('hola', '123') falla."
echo -e " 3. Comprobar que no puedes iniciar sesión sin verificar cuenta."
echo -e " 4. Leer token único en BD / correo e introducir enlace para activar."
echo ""
find_code "Validación Estricta Contraseñas" "Validator::password|preg_match" "app/core/Validator.php" "app/controllers/AuthController.php"
pause

# --- PASO 4: USER AUTHENTICATION ---
print_header
echo -e "${C_B}▶ PASO 4: USER AUTHENTICATION (LOGIN/LOGOUT/RESET)${C_DF}"
echo -e "¿Qué probar en la web?"
echo -e " 1. Autenticarse correctamente."
echo -e " 2. Cerrar sesión y comprobar que las rutas protegidas redirigen."
echo -e " 3. Flujo completo de 'Olvidé mi contraseña'."
echo ""
find_code "Login / Reset" "password_verify|resetPassword|createResetToken" "app/controllers/AuthController.php" "app/models/User.php"
pause

# --- PASO 5: EDITOR (FT_SNAPCHAT) ---
print_header
echo -e "${C_B}▶ PASO 5: ft_snapchat (EDITOR)${C_DF}"
echo -e "¿Qué probar en la web?"
echo -e " 1. La webcam se activa (getUserMedia)."
echo -e " 2. Vista previa en vivo del sticker + posibilidad de arrastrarlo."
echo -e " 3. El botón de captura está DESHABILITADO si no hay sticker seleccionado."
echo -e " 4. Modo UPLOAD: acepta 0 o 1 pegatina."
echo -e " 5. El proceso de unión se efectúa en el Server Side (GD)."
echo ""
find_code "Procesado Server-side (Merge)" "imagecreatefromstring|imagecopy|mergeImages" "app/controllers/EditorController.php"
find_code "Flexibilidad de subida" "\$_FILES" "app/controllers/EditorController.php"
pause

# --- PASO 6: GALLERY (FT_INSTAGRAM) ---
print_header
echo -e "${C_B}▶ PASO 6: ft_instagram (GALERÍA PÚBLICA)${C_DF}"
echo -e "¿Qué probar en la web?"
echo -e " 1. Accesible con y sin estar logueado."
echo -e " 2. Lista las imágenes por fecha y con Paginación (Máx 5 por página)."
echo -e " 3. Estando logueado puedes dar Like y Comentar."
echo -e " 4. Al comentar, el creador de la foto recibe Email (si tiene la preferencia)."
echo ""
find_code "Paginación" "perPage|getAll" "app/controllers/GalleryController.php"
find_code "Notificación por e-mail" "notify_comments|sendCommentNotification" "app/controllers/GalleryController.php"
pause

# --- PASO 7: USER PREFERENCES & CANCANCAN ---
print_header
echo -e "${C_B}▶ PASO 7: PREFERENCIAS & DERECHOS (CANCANCAN)${C_DF}"
echo -e "¿Qué probar en la web?"
echo -e " 1. Perfil -> Modificar Email, Usuario o Contraseña (sin warning/error)."
echo -e " 2. Activar/Desactivar mails de notificaciones."
echo -e " 3. Borrar tu propio montaje del editor (pero NO el de los demás)."
echo -e " 4. Entrar en '/editor' estando de incógnito -> Redirige al login."
echo ""
find_code "Control de Acceso y Redirección" "requireAuth|requireGuest" "app/core/Controller.php" "app/controllers/EditorController.php"
pause

# --- PASO 8: SECURITY (LA MÁS CRÍTICA) ---
print_header
echo -e "${C_B}▶ PASO 8: SECURITY (SECURITY FIRST DAMNIT!)${C_DF}"
echo -e "${C_R}1. DB CIPHER:${C_DF} Contraseñas son puramente BCRYPT."
find_code "Hashing Seguro" "password_hash" "app/models/User.php"
echo -e "\n${C_R}2. XSS TEST:${C_DF} Intenta enviar un comentario con: <script>alert('GAME');</script>"
echo -e "   -> Debe renderizarse como texto puro por htmlspecialchars."
find_code "Defensa XSS" "htmlspecialchars" "app/core/Controller.php"
echo -e "\n${C_R}3. SQL INJECTION TEST:${C_DF} Logout, e intenta Log In con la contraseña: blahblah' OR 1='1"
echo -e "   -> NO debes acceder a la app."
find_code "Defensa SQLi con PDO" "prepare|ERRMODE_EXCEPTION" "app/core/Database.php"
pause

# --- PASO 9: UI/UX, MOBILE & BONUS ---
print_header
echo -e "${C_B}▶ PASO 9: UI/UX, MOBILE & BONUS${C_DF}"
echo -e "Abre DevTools de Chrome e inspecciona tamaño móvil."
echo -e "El inspector mirará tus bonus. Recuerda los Bonus implementados:"
echo -e " 🏆 (Bonus 1) Interfaz AJAXificada (likes + comentarios)."
echo -e " 🏆 (Bonus 2) Previsualización en vivo (Pegatina sobre Webcam + drag)."
echo -e " 🏆 (Bonus 3) Scroll Infinito en la galería."
echo -e " 🏆 (Bonus 4) GIF animado (Imagick / ImageMagick) — funciona out-of-the-box."
echo -e " 🏆 (Bonus 5) Sistema completo de reset de contraseña por email."
echo ""
echo -e "Comprueba el GIF:"
echo -e "  ${C_M}docker-compose exec web php -m | grep imagick${C_DF}"
echo ""
echo -e "${C_G}¡EVALUACIÓN CONCLUIDA! Ya puedes destruir todo con 'docker-compose down -v'${C_DF}\n"
echo -e "Más información: ${C_C}https://www.42malaga.com/${C_DF}\n"

exit 0
