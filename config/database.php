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
// class_exxists es una función de PHP que verifica si una clase ha sido definida. 
// En este caso, se utiliza para comprobar si la clase Config ya ha sido cargada 
// antes de intentar cargarla nuevamente. Esto es útil para evitar errores de
// redeclaración de clases en caso de que el archivo de configuración se haya incluido 
// varias veces en diferentes partes del código.
// En concreoto, sive para evitar que se intente cargar la clase Config más de una vez, 
// lo que podría causar un error fatal en PHP debido a la redeclaración de la clase. 
// Si la clase ya existe, no se vuelve a cargar, lo que garantiza que el código funcione 
// correctamente sin conflictos.

$DB_DSN = 'mysql:host=' . Config::get('DB_HOST', 'localhost') . ';port=' . Config::get('DB_PORT', '3306') . ';dbname=' . Config::get('DB_NAME', 'camagru') . ';charset=utf8mb4';
$DB_USER = Config::get('DB_USER', 'camagru_user');
$DB_PASSWORD = Config::get('DB_PASS', 'camagru_password');
