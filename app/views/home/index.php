<?php $title = function_exists('__') ? __('home.welcome') : 'Welcome'; include __DIR__ . '/../partials/header.php'; ?>

<div class="hero">
    <h1><?php echo function_exists('__') ? __('home.welcome') : 'Welcome to Camagru'; ?> 📸</h1>
    <p><?php echo function_exists('__') ? __('home.subtitle') : 'Create amazing photos with your webcam and fun stickers!'; ?></p>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/editor" class="btn">
            <?php echo function_exists('__') ? __('home.get_started') : 'Get Started'; ?>
        </a>
    <?php else: ?>
        <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/register" class="btn">
            <?php echo function_exists('__') ? __('home.get_started') : 'Get Started'; ?>
        </a>
        <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/gallery" class="btn btn-secondary">
            <?php echo function_exists('__') ? __('home.view_gallery') : 'View Gallery'; ?>
        </a>
    <?php endif; ?>
</div>

<div class="card features-card">
    <h2 class="features-title">✨ <?php echo function_exists('__') ? __('home.features') : 'Features'; ?></h2>
    <p class="features-hint"><?php echo function_exists('__') ? __('home.features_hint') : 'Click a feature to learn more'; ?></p>

    <div class="features-grid">
        <button type="button" class="feature-tile" data-feature="webcam" aria-expanded="false">
            <span class="feature-icon" aria-hidden="true">📷</span>
            <span class="feature-name"><?php echo function_exists('__') ? __('home.feat_webcam') : 'Webcam'; ?></span>
            <span class="feature-detail"><?php echo function_exists('__') ? __('home.feat_webcam_help') : 'Open the Editor, allow camera access, pick a sticker and capture. Composition is done on the server.'; ?></span>
        </button>
        <button type="button" class="feature-tile" data-feature="stickers" aria-expanded="false">
            <span class="feature-icon" aria-hidden="true">🎨</span>
            <span class="feature-name"><?php echo function_exists('__') ? __('home.feat_stickers') : 'Stickers'; ?></span>
            <span class="feature-detail"><?php echo function_exists('__') ? __('home.feat_stickers_help') : 'Choose a sticker, drag it on the preview and resize it. You must select a sticker before capturing.'; ?></span>
        </button>
        <button type="button" class="feature-tile" data-feature="social" aria-expanded="false">
            <span class="feature-icon" aria-hidden="true">❤️</span>
            <span class="feature-name"><?php echo function_exists('__') ? __('home.feat_social') : 'Social'; ?></span>
            <span class="feature-detail"><?php echo function_exists('__') ? __('home.feat_social_help') : 'In the Gallery you can like photos and leave comments. Owners can get email notifications if enabled in Settings.'; ?></span>
        </button>
        <button type="button" class="feature-tile" data-feature="secure" aria-expanded="false">
            <span class="feature-icon" aria-hidden="true">🔒</span>
            <span class="feature-name"><?php echo function_exists('__') ? __('home.feat_secure') : 'Secure'; ?></span>
            <span class="feature-detail"><?php echo function_exists('__') ? __('home.feat_secure_help') : 'Passwords are hashed, forms use CSRF tokens, and database access uses prepared statements.'; ?></span>
        </button>
    </div>
</div>

<script>
(function () {
    var tiles = document.querySelectorAll('.feature-tile');
    tiles.forEach(function (tile) {
        tile.addEventListener('click', function () {
            var open = tile.classList.contains('is-open');
            tiles.forEach(function (t) {
                t.classList.remove('is-open');
                t.setAttribute('aria-expanded', 'false');
            });
            if (!open) {
                tile.classList.add('is-open');
                tile.setAttribute('aria-expanded', 'true');
            }
        });
    });
})();
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
