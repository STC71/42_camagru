#!/bin/bash

# ============================================================================
#  📸 CAMAGRU - TEST DE EVALUACIÓN INTERACTIVA (125% EDITION) 📸
# ============================================================================
#  Test guiado paso a paso para la defensa de Camagru en 42
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
    echo -e "${C_B}│${C_DF}                         by sternero                       ${C_B}│${C_DF}"
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
            lines=$(grep -n -E "$search" "$file" | awk -F: '{print $1}' | paste -sd, -)
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
echo -e "${C_W}de Camagru, siguiendo escrupulosamente la hoja oficial de 42 EvalHub.${C_DF}"
pause

# --- PASO 1: VERIFICACIONES PREVIAS ---
print_header
echo -e "${C_B}▶ PASO 1: PREFLIGHT CHECKS (OBLIGATORIOS)${C_DF}"
echo -e "Los evaluadores comprobarán que tienes los ficheros obligatorios:\n"
echo -e " - ${C_G}index.php${C_DF} (En la raíz)"
echo -e " - ${C_G}config/database.php${C_DF}"
echo -e " - ${C_G}config/setup.php${C_DF}\n"
find_code "Archivos Mandatorios" "require_once" "index.php" "config/database.php" "config/setup.php"
echo -e "\nTambién verifican la instanciación de PDO con PDO::ERRMODE_EXCEPTION."
find_code "Modo de Errores PDO" "ERRMODE_EXCEPTION" "app/core/Database.php" "config/setup.php"
pause

# --- PASO 2: IGNICIÓN (SERVER START) ---
print_header
echo -e "${C_B}▶ PASO 2: 3..2..1.. IGNITION${C_DF}"
echo -e "Comando a ejecutar por el evaluador o ti mismo:"
echo -e "  ${C_M}make up${C_DF}"
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
echo -e " 2. Realizar Logout desde el Header."
echo -e " 3. Darle a 'Forgot password?' e iniciar el flujo de reestablecimiento."
echo ""
find_code "Autenticación segura" "password_verify|destroy" "app/controllers/AuthController.php" "app/models/User.php"
pause

# --- PASO 5: EDITOR (FT_SNAPCHAT) ---
print_header
echo -e "${C_B}▶ PASO 5: ft_snapchat (WORKSPACE DE EDICIÓN)${C_DF}"
echo -e "¿Qué probar en la web?"
echo -e " 1. Header, Main y Footer decéntemente posicionados."
echo -e " 2. Vista de cámara activa + lista de imágenes anteriores + pegatinas."
echo -e " 3. Botón de foto bloqueado si no hay pegatina seleccionada (Modo Webcam)."
echo -e " 4. Modo UPLOAD (Subida PC): Debe aceptar 0 pegatinas, 1, o múltiples."
echo -e " 5. El proceso de unión se efectúa en el Server Side."
echo ""
find_code "Procesado Server-side (Merge)" "imagecreatefromstring|imagecopy" "app/controllers/EditorController.php"
echo ""
find_code "Flexibilidad de subida (Upload 0 stickers)" "\$_FILES\['image'\]" "public/js/editor.js" "app/controllers/EditorController.php"
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
find_code "Paginación" "\\\$perPage =" "app/controllers/GalleryController.php"
find_code "Notificación por e-mail" "notify_comments" "app/controllers/GalleryController.php"
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
find_code "Defensa XSS" "htmlspecialchars" "app/core/Controller.php" "app/views/gallery/index.php"
echo -e "\n${C_R}3. SQL INJECTION TEST:${C_DF} Logout, e intenta Log In con la contraseña (sin llaves): blahblah' OR 1='1"
echo -e "   -> NO debes acceder a la app."
find_code "Defensa SQLi con PDO" "prepare|execute" "app/core/Database.php"
pause

# --- PASO 9: RESPONSIVE & BONUS ---
print_header
echo -e "${C_B}▶ PASO 9: UI/UX, MOBILE & BONUS${C_DF}"
echo -e "Abre DevTools de Chrome e inspecciona tamaño móvil."
echo -e "El inspector mirará tus bonus. Recuerda tus Bonus implementados:"
echo -e " 🏆 (Bonus 1) Interfaz AJAXificada."
echo -e " 🏆 (Bonus 2) Previsualización en vivo (Pegatina sobre Webcam)."
echo -e " 🏆 (Bonus 3) Scroll Infinito (AJAX) en la Paginación."
echo -e " 🏆 (Bonus 4) Reset de Contraseñas (Sistema completo Email)."
echo -e " 🏆 (Bonus 5) Botones de Share Social (Compartir)."
echo ""
echo -e "${C_G}¡EVALUACIÓN CONCLUIDA! Ya puedes destruir todo con 'make fclean'${C_DF}\n"

exit 0