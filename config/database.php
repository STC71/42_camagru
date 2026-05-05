<?php
/**
 * Database Configuration
 * Required by 42 Subject
 */

require_once __DIR__ . '/Config.php';

// Si no se han cargado las variables, las cargamos
// En algunas plataformas puede fallar si no comprobamos si Config ya fue cargado
if (class_exists('Config')) {
    Config::load();
}

$DB_DSN = 'mysql:host=' . Config::get('DB_HOST', 'localhost') . ';port=' . Config::get('DB_PORT', '3306') . ';dbname=' . Config::get('DB_NAME', 'camagru') . ';charset=utf8mb4';
$DB_USER = Config::get('DB_USER', 'camagru_user');
$DB_PASSWORD = Config::get('DB_PASS', 'camagru_password');
