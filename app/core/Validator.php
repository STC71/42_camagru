<?php

/**
 * =============================================================================
 * Validator.php - Validaciones y saneamiento de datos
 * =============================================================================
 * Esta clase centraliza todas las reglas de validación de la aplicación.
 * Así no tenemos que repetir el mismo código en cada controlador.
 *
 * Analogía cotidiana:
 * Es como el portero de un edificio. Antes de dejar pasar a alguien,
 * comprueba si tiene el carnet, si la foto coincide, si está en la lista, etc.
 * =============================================================================
 */
class Validator {

    /**
     * Comprueba si un texto es un email válido
     */
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Comprueba si un username es válido
     * Reglas: entre 3 y 20 caracteres, solo letras, números y guion bajo
     */
    public static function username($username) {
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
    }

    /**
     * Comprueba si una contraseña es suficientemente segura
     * Reglas: mínimo 8 caracteres + al menos una letra + al menos un número
     *
     * Ejemplo que FALLA:  "hola"        (muy corta y sin números)
     * Ejemplo que PASA:   "Hola1234"    (cumple todo)
     */
    public static function password($password) {
        return strlen($password) >= 8
            && preg_match('/[a-zA-Z]/', $password)
            && preg_match('/[0-9]/', $password);
    }

    /**
     * Comprueba que un campo no esté vacío
     */
    public static function required($value) {
        return !empty(trim($value));
    }

    /**
     * Comprueba longitud mínima
     */
    public static function minLength($value, $min) {
        return strlen(trim($value)) >= $min;
    }

    /**
     * Comprueba longitud máxima
     */
    public static function maxLength($value, $max) {
        return strlen(trim($value)) <= $max;
    }

    /**
     * Comprueba que dos valores sean exactamente iguales
     * (útil para confirmar contraseña)
     */
    public static function match($value1, $value2) {
        return $value1 === $value2;
    }

    /**
     * Limpia un nombre de archivo para que sea seguro
     * (quita rutas y caracteres peligrosos)
     */
    public static function sanitizeFilename($filename) {
        $filename = basename($filename); // Quita cualquier ruta
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        return $filename;
    }

    /**
     * Comprueba que un archivo subido sea realmente una imagen
     * (no nos fiamos solo de la extensión, miramos el contenido real)
     */
    public static function isImage($file) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file);
        finfo_close($finfo);
        return in_array($mimeType, $allowedTypes);
    }

    /**
     * Comprueba que el archivo no supere el tamaño máximo (por defecto 5 MB)
     */
    public static function maxFileSize($file, $maxSize = 5242880) {
        return filesize($file) <= $maxSize;
    }
}
