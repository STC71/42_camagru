<?php

/**
 * Reúne validaciones y utilidades de saneamiento reutilizables.
 */
class Validator {
    
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function username($username) {
        // Entre 3 y 20 caracteres; letras, números y guion bajo.
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
    }

    public static function password($password) {
        // Mínimo 8 caracteres, con al menos una letra y un número.
        return strlen($password) >= 8 && 
               preg_match('/[a-zA-Z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }

    public static function required($value) {
        return !empty(trim($value));
    }

    public static function minLength($value, $min) {
        return strlen(trim($value)) >= $min;
    }

    public static function maxLength($value, $max) {
        return strlen(trim($value)) <= $max;
    }

    public static function match($value1, $value2) {
        return $value1 === $value2;
    }

    public static function sanitizeFilename($filename) {
        // Elimina rutas y caracteres no permitidos.
        $filename = basename($filename);
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        return $filename;
    }

    public static function isImage($file) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file);
        finfo_close($finfo);
        return in_array($mimeType, $allowedTypes);
    }

    public static function maxFileSize($file, $maxSize = 5242880) { // 5MB default
        // 5 MB por defecto.
        return filesize($file) <= $maxSize;
    }
}
