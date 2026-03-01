<?php

/**
 * Validator Class
 * Handles input validation
 */
class Validator {
    
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function username($username) {
        // Username must be 3-20 characters, alphanumeric and underscore only
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
    }

    public static function password($password) {
        // Password must be at least 8 characters, contain at least one letter and one number
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
        // Remove any path information
        $filename = basename($filename);
        // Remove any special characters
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
        return filesize($file) <= $maxSize;
    }
}
