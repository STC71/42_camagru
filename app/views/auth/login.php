<?php $title = 'Login'; include __DIR__ . '/../partials/header.php'; ?>

<div class="card" style="max-width: 500px; margin: 2rem auto;">
    <h1>Login to Camagru</h1>
    
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-group">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Login</button>
        </div>
    </form>
    
    <p style="text-align: center; margin-top: 1rem;">
        Don't have an account? <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/register">Register</a><br>
        <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/forgot-password">Forgot password?</a>
    </p>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
