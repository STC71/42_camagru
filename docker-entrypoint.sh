#!/bin/bash
set -e

# Esperar a que los volúmenes estén montados y corregir permisos
echo "Corrigiendo permisos..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 777 /var/www/html/public/uploads
chmod 600 /var/www/html/.env 2>/dev/null || true

echo "Permisos corregidos. Iniciando Apache..."

# Ejecutar el comando original de Apache
exec apache2-foreground
