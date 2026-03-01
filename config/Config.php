<?php

/**
 * Config Class
 * Manages application configuration and environment variables
 */
class Config {
    private static $config = [];

    public static function load() {
        // Load .env file
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '#') === 0) continue;
                list($key, $value) = explode('=', $line, 2);
                self::$config[trim($key)] = trim($value);
                putenv("$key=$value");
            }
        }
    }

    public static function get($key, $default = null) {
        return self::$config[$key] ?? getenv($key) ?: $default;
    }
}
