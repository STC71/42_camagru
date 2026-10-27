<?php
/**
 * Cabecera global: navegación, banderas de idioma y usuario de sesión.
 * No altera la lógica de negocio; solo presentación.
 */
$appUrl = rtrim(
    (class_exists('Config') ? Config::get('APP_URL', 'http://localhost:8080') : null)
        ?: ($_ENV['APP_URL'] ?? getenv('APP_URL') ?: 'http://localhost:8080'),
    '/'
);
$currentLang = class_exists('I18n') ? I18n::getLang() : 'es';
$htmlLang = htmlspecialchars($currentLang, ENT_QUOTES, 'UTF-8');
// URL actual sin query lang (para cambiar idioma sin perder ruta)
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
?>
<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Camagru - Photo sharing application with webcam and stickers">
    <title><?php echo htmlspecialchars($title ?? 'Camagru'); ?> - Camagru</title>
    <link rel="stylesheet" href="<?php echo $appUrl; ?>/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-bar">
            <a href="<?php echo $appUrl; ?>/" class="logo">📸 Camagru</a>

            <!-- Usuario de sesión (siempre visible) -->
            <div class="session-badge" title="<?php echo isset($_SESSION['user_id']) ? 'Session' : 'Guest'; ?>">
                <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['username'])): ?>
                    <span class="session-dot online"></span>
                    <span class="session-label"><?php echo function_exists('__') ? __('session.logged_as') : 'Conectado:'; ?></span>
                    <strong class="session-user"><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                <?php else: ?>
                    <span class="session-dot offline"></span>
                    <span class="session-label"><?php echo function_exists('__') ? __('session.guest') : 'Visitante'; ?></span>
                <?php endif; ?>
            </div>

            <!-- Selector de idioma (banderas) -->
            <div class="lang-switcher" role="navigation" aria-label="<?php echo function_exists('__') ? __('nav.lang') : 'Idioma'; ?>">
                <?php if (class_exists('I18n')): ?>
                    <?php foreach (I18n::$supported as $code => $meta): ?>
                        <?php
                            $sep = (strpos($requestPath, '?') !== false) ? '&' : '?';
                            // Preferimos path + ?lang=
                            $href = $requestPath . '?lang=' . urlencode($code);
                        ?>
                        <a href="<?php echo htmlspecialchars($href); ?>"
                           class="lang-flag<?php echo $code === $currentLang ? ' active' : ''; ?>"
                           title="<?php echo htmlspecialchars($meta['name']); ?>"
                           hreflang="<?php echo htmlspecialchars($code); ?>">
                            <span aria-hidden="true"><?php echo $meta['flag']; ?></span>
                            <span class="sr-only"><?php echo htmlspecialchars($meta['name']); ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <nav>
                <ul>
                    <li><a href="<?php echo $appUrl; ?>/"><?php echo function_exists('__') ? __('nav.home') : 'Home'; ?></a></li>
                    <li><a href="<?php echo $appUrl; ?>/gallery"><?php echo function_exists('__') ? __('nav.gallery') : 'Gallery'; ?></a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo $appUrl; ?>/editor"><?php echo function_exists('__') ? __('nav.editor') : 'Editor'; ?></a></li>
                        <li><a href="<?php echo $appUrl; ?>/profile"><?php echo function_exists('__') ? __('nav.profile') : 'Profile'; ?></a></li>
                        <li><a href="<?php echo $appUrl; ?>/settings"><?php echo function_exists('__') ? __('nav.settings') : 'Settings'; ?></a></li>
                        <li><a href="<?php echo $appUrl; ?>/logout"><?php echo function_exists('__') ? __('nav.logout') : 'Logout'; ?></a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $appUrl; ?>/login"><?php echo function_exists('__') ? __('nav.login') : 'Login'; ?></a></li>
                        <li><a href="<?php echo $appUrl; ?>/register"><?php echo function_exists('__') ? __('nav.register') : 'Register'; ?></a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="flash flash-success">
                    <?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="flash flash-error">
                    <?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
                </div>
            <?php endif; ?>
        </div>
