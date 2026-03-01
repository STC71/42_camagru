<?php $title = 'Register'; include __DIR__ . '/../partials/header.php'; ?>

<div class="card" style="max-width: 500px; margin: 2rem auto;">
    <h1>Create Account</h1>
    
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required 
                   pattern="[a-zA-Z0-9_]{3,20}" 
                   title="3-20 characters, alphanumeric and underscore only"
                   value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>">
            <small>3-20 characters, alphanumeric and underscore only</small>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required
                   value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required
                   pattern=".{8,}"
                   title="Minimum 8 characters with letters and numbers">
            <small>Minimum 8 characters with letters and numbers</small>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Register</button>
        </div>
    </form>
    
    <p style="text-align: center; margin-top: 1rem;">
        Already have an account? <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/login">Login</a>
    </p>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
