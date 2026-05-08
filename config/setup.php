<?php
/**
 * Script de Configuracion de Base de Datos
 * Required by 42 Subject
 * Can be run via CLI or browser to initialize the database
 */

require_once __DIR__ . '/database.php';

try {
    // Conectamos sin el nombre de la BD para poder crearla si no existe
    $dsn_no_db = 'mysql:host=' . Config::get('DB_HOST', 'localhost') . ';port=' . Config::get('DB_PORT', '3306') . ';charset=utf8mb4';
    
    $pdo = new PDO($dsn_no_db, $DB_USER, $DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Leemos el script inicial de la BD
    $sql_file = __DIR__ . '/../database/init.sql';
    
    if (!file_exists($sql_file)) {
        die("Error: No se encuentra el archivo init.sql en " . $sql_file . "\n");
    }
    
    $sql = file_get_contents($sql_file);
    
    // Ejecutamos el SQL completo
    $pdo->exec($sql);
    
    echo "Base de datos configurada e inicializada correctamente.\n";
} catch (PDOException $e) {
    die("Error durante el setup de la base de datos: " . $e->getMessage() . "\n");
}
