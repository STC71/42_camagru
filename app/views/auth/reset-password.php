<?php $title = 'Reset Password'; include __DIR__ . '/../partials/header.php'; ?>

<div class="card" style="max-width: 500px; margin: 2rem auto;">
    <h1>Reset Your Password</h1>
    
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        
        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" required
                   pattern=".{8,}"
                   title="Minimum 8 characters with letters and numbers">
            <small>Minimum 8 characters with letters and numbers</small>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Reset Password</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
