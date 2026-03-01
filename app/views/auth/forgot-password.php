<?php $title = 'Forgot Password'; include __DIR__ . '/../partials/header.php'; ?>

<div class="card" style="max-width: 500px; margin: 2rem auto;">
    <h1>Reset Password</h1>
    <p>Enter your email address and we'll send you a link to reset your password.</p>
    
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Send Reset Link</button>
        </div>
    </form>
    
    <p style="text-align: center; margin-top: 1rem;">
        <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/login">Back to Login</a>
    </p>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
