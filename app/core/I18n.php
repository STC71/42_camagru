<?php
/**
 * =============================================================================
 * I18n.php - Internacionalización (multi-idioma)
 * =============================================================================
 * Idioma por defecto: español (es).
 * Se guarda en $_SESSION['lang'] y cookie 'camagru_lang' (30 días).
 * Uso en vistas: <?= __('nav.home') ?>
 * =============================================================================
 */
class I18n {
    /** @var string */
    private static $lang = 'es';

    /** @var array */
    private static $messages = [];

    /** Idiomas soportados */
    public static $supported = [
        'es' => ['name' => 'Español',   'flag' => '🇪🇸'],
        'en' => ['name' => 'English',   'flag' => '🇬🇧'],
        'pt' => ['name' => 'Português', 'flag' => '🇵🇹'],
        'de' => ['name' => 'Deutsch',   'flag' => '🇩🇪'],
        'fr' => ['name' => 'Français',  'flag' => '🇫🇷'],
    ];

    /**
     * Inicializa el idioma (llamar tras session_start).
     * Prioridad: ?lang=xx → sesión → cookie → 'es'
     */
    public static function init() {
        $chosen = null;

        if (isset($_GET['lang']) && isset(self::$supported[$_GET['lang']])) {
            $chosen = $_GET['lang'];
            $_SESSION['lang'] = $chosen;
            setcookie('camagru_lang', $chosen, time() + 60 * 60 * 24 * 30, '/');
        } elseif (!empty($_SESSION['lang']) && isset(self::$supported[$_SESSION['lang']])) {
            $chosen = $_SESSION['lang'];
        } elseif (!empty($_COOKIE['camagru_lang']) && isset(self::$supported[$_COOKIE['camagru_lang']])) {
            $chosen = $_COOKIE['camagru_lang'];
            $_SESSION['lang'] = $chosen;
        } else {
            $chosen = 'es';
            $_SESSION['lang'] = $chosen;
        }

        self::$lang = $chosen;
        self::loadMessages($chosen);
    }

    private static function loadMessages($lang) {
        $file = __DIR__ . '/../lang/' . $lang . '.php';
        if (is_file($file)) {
            self::$messages = require $file;
        } else {
            $fallback = __DIR__ . '/../lang/es.php';
            self::$messages = is_file($fallback) ? require $fallback : [];
        }
    }

    public static function getLang() {
        return self::$lang;
    }

    /**
     * Traduce una clave. Si falta, devuelve la clave o el fallback.
     * Soporta placeholders: __('hello', ['name' => 'Ana']) con ":name" en el texto.
     */
    public static function t($key, $replace = []) {
        $text = self::$messages[$key] ?? $key;
        foreach ($replace as $k => $v) {
            $text = str_replace(':' . $k, (string)$v, $text);
        }
        return $text;
    }
}

/** Atajo global para vistas */
function __($key, $replace = []) {
    return I18n::t($key, $replace);
}
