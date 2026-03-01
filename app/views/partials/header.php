<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Camagru - Photo sharing application with webcam and stickers">
    <title><?php echo $title ?? 'Camagru'; ?> - Camagru</title>
    <link rel="stylesheet" href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/" class="logo">📸 Camagru</a>
            <nav>
                <ul>
                    <li><a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/">Home</a></li>
                    <li><a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/gallery">Gallery</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/editor">Editor</a></li>
                        <li><a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/profile">Profile</a></li>
                        <li><a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/settings">Settings</a></li>
                        <li><a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/login">Login</a></li>
                        <li><a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/register">Register</a></li>
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
