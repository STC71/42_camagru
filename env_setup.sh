#!/bin/bash

# =============================================================================
# env_setup.sh - Asistente interactivo de configuración de entorno
# =============================================================================
# Genera un archivo .env personalizado preguntando los valores necesarios.
# Sugiere valores por defecto sensatos (incluido el usuario del sistema).
# =============================================================================

# Colors for pretty output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color
BOLD='\033[1m'

# Usuario del sistema actual (para sugerencias personalizadas)
CURRENT_USER=$(whoami 2>/dev/null || echo "camagru_user")

# Clear screen and show banner
clear
echo -e "${PURPLE}${BOLD}"
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║              CONFIGURACIÓN DE ENTORNO CAMAGRU                  ║"
echo "║                  Asistente de Configuración                    ║"
echo "║                 Usuario detectado:  ${CURRENT_USER}                   ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Check if .env already exists
if [ -f ".env" ]; then
    echo -e "${YELLOW}⚠️  Advertencia: ¡El archivo .env ya existe!${NC}"
    read -p "¿Quieres sobreescribirlo? (s/N): " overwrite
    if [[ ! $overwrite =~ ^[SsYy]$ ]]; then
        echo -e "${RED}❌ Configuración cancelada.${NC}"
        exit 0
    fi
    echo ""
fi

# Function to prompt for input with default value
prompt_with_default() {
    local prompt="$1"
    local default="$2"
    local is_password="${3:-false}"
    local value=""

    if [ "$is_password" = "true" ]; then
        echo -e "${CYAN}${BOLD}${prompt}${NC}" >&2
        read -s -p "Ingresa el valor (oculto, Enter = usar por defecto): " value
        echo "" >&2
        if [ -z "$value" ]; then
            value="$default"
        fi
    else
        read -p "$(echo -e "${CYAN}${BOLD}${prompt}${NC} ${BLUE}[por defecto: ${default}]${NC}: ")" value >&2
        if [ -z "$value" ]; then
            value="$default"
        fi
    fi

    echo "$value"
}

# Function to generate random string for secrets
generate_random_string() {
    openssl rand -base64 32 2>/dev/null | tr -d "=+/" | cut -c1-32 || \
    head -c 32 /dev/urandom | base64 | tr -d "=+/" | cut -c1-32
}

echo -e "${GREEN}${BOLD}📦 CONFIGURACIÓN DE BASE DE DATOS${NC}"
echo -e "${YELLOW}Configura los ajustes de conexión a la base de datos${NC}"
echo -e "${YELLOW}💡 Se sugiere el usuario del sistema (${CURRENT_USER}) como base del nombre de usuario${NC}"
echo ""

DB_HOST=$(prompt_with_default "Host de Base de Datos:" "db")
DB_PORT=$(prompt_with_default "Puerto de Base de Datos:" "3306")
DB_NAME=$(prompt_with_default "Nombre de Base de Datos:" "camagru")
# Sugerimos un usuario basado en whoami para que sea más personal
DB_USER=$(prompt_with_default "Usuario de Base de Datos:" "${CURRENT_USER}_camagru")
DB_PASS=$(prompt_with_default "Contraseña de Base de Datos:" "camagru_password" "true")
DB_ROOT_PASS=$(prompt_with_default "Contraseña Root de Base de Datos:" "root_password" "true")

echo ""
echo -e "${GREEN}${BOLD}🌐 CONFIGURACIÓN DE APLICACIÓN${NC}"
echo -e "${YELLOW}Define la URL de tu aplicación${NC}"
echo ""

APP_URL=$(prompt_with_default "URL de la Aplicación:" "http://localhost:8080")

echo ""
echo -e "${GREEN}${BOLD}📧 CONFIGURACIÓN DE CORREO${NC}"
echo -e "${YELLOW}Configura los ajustes SMTP para envío de correos${NC}"
echo -e "${YELLOW}💡 Consejo: Usa Mailtrap.io (https://mailtrap.io) para pruebas${NC}"
echo ""

MAIL_HOST=$(prompt_with_default "Host SMTP:" "sandbox.smtp.mailtrap.io")
MAIL_PORT=$(prompt_with_default "Puerto SMTP:" "2525")
MAIL_USER=$(prompt_with_default "Usuario SMTP:" "your_mailtrap_user")
MAIL_PASS=$(prompt_with_default "Contraseña SMTP:" "your_mailtrap_password" "true")
MAIL_FROM=$(prompt_with_default "Dirección de Correo Remitente:" "noreply@camagru.local")

echo ""
echo -e "${GREEN}${BOLD}🔐 CONFIGURACIÓN DE SEGURIDAD${NC}"
echo -e "${YELLOW}Genera un secreto de sesión seguro${NC}"
echo ""

read -p "$(echo -e ${CYAN}¿Generar SESSION_SECRET aleatorio? \(recomendado\) \(S/n\):${NC} )" generate_secret
if [[ ! $generate_secret =~ ^[Nn]$ ]]; then
    SESSION_SECRET=$(generate_random_string)
    echo -e "${GREEN}✓ Secreto aleatorio seguro generado${NC}"
else
    SESSION_SECRET=$(prompt_with_default "Secreto de Sesión:" "change_this_to_a_random_string_for_security")
fi

# Create .env file
echo ""
echo -e "${BLUE}${BOLD}📝 Creando archivo .env...${NC}"

cat > .env << EOF
# =============================================================================
# Camagru - Variables de entorno
# Generado automáticamente por env_setup.sh
# Usuario del sistema: ${CURRENT_USER}
# =============================================================================

# Configuración de Base de Datos
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_NAME=${DB_NAME}
DB_USER=${DB_USER}
DB_PASS=${DB_PASS}
DB_ROOT_PASS=${DB_ROOT_PASS}

# URL de la Aplicación
APP_URL=${APP_URL}

# Configuración de Correo (usa un servicio como Mailtrap para pruebas)
MAIL_HOST=${MAIL_HOST}
MAIL_PORT=${MAIL_PORT}
MAIL_USER=${MAIL_USER}
MAIL_PASS=${MAIL_PASS}
MAIL_FROM=${MAIL_FROM}

# Seguridad
SESSION_SECRET=${SESSION_SECRET}
EOF

# Set proper permissions
chmod 600 .env

echo ""
echo -e "${GREEN}${BOLD}✅ ¡ÉXITO!${NC}"
echo -e "${GREEN}¡Tu archivo .env ha sido creado exitosamente!${NC}"
echo ""
echo -e "${YELLOW}⚠️  Importante:${NC}"
echo -e "  • El archivo .env contiene información sensible"
echo -e "  • Ya está excluido de git (.gitignore)"
echo -e "  • Permisos establecidos a 600 (solo lectura/escritura del propietario)"
echo ""
echo -e "${GREEN}${BOLD}🚀 Siguientes pasos:${NC}"
echo -e "  1. Revisa tu archivo .env si es necesario"
echo -e "  2. Inicia tu aplicación con: ${CYAN}make up${NC}  o  ${CYAN}docker-compose up --build -d${NC}"
echo -e "  3. Accede a tu app en: ${CYAN}${APP_URL}${NC}"
echo ""
echo -e "${PURPLE}${BOLD}¡Feliz codificación! 🎉${NC}"
