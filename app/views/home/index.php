<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="hero" style="text-align: center; padding: 4rem 0; color: white;">
    <h1 style="font-size: 3rem; margin-bottom: 1rem;">Welcome to Camagru 📸</h1>
    <p style="font-size: 1.5rem; margin-bottom: 2rem;">Create amazing photos with your webcam and fun stickers!</p>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/register" class="btn" style="margin: 0.5rem;">Get Started</a>
        <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/gallery" class="btn btn-secondary" style="margin: 0.5rem;">View Gallery</a>
    <?php else: ?>
        <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/editor" class="btn" style="margin: 0.5rem;">Create Photo</a>
        <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/gallery" class="btn btn-secondary" style="margin: 0.5rem;">View Gallery</a>
    <?php endif; ?>
</div>

<div class="card">
    <h2>✨ Features</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem;">
        <div style="text-align: center;">
            <h3>📷 Webcam Capture</h3>
            <p>Take photos directly from your browser</p>
        </div>
        <div style="text-align: center;">
            <h3>🎨 Fun Stickers</h3>
            <p>Apply creative stickers to your photos</p>
        </div>
        <div style="text-align: center;">
            <h3>❤️ Like & Comment</h3>
            <p>Engage with the community</p>
        </div>
        <div style="text-align: center;">
            <h3>🔒 Secure</h3>
            <p>Your data is safe with us</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
