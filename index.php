<?php
/**
 * Main Entry Point
 * Required by 42 Subject
 */

// Cambiamos el directorio activo a public para mantener las rutas relativas
chdir(__DIR__ . '/public');

// Redirigimos el flujo al archivo real de la aplicación
require_once 'index.php';
