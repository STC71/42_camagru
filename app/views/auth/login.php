<?php $title = function_exists('__') ? __('login.title') : 'Login'; include __DIR__ . '/../partials/header.php'; ?>

<div class="auth-wrap">
    <div class="card auth-card">
        <h1><?php echo function_exists('__') ? __('login.title') : 'Login'; ?></h1>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

            <div class="form-group">
                <label for="username"><?php echo function_exists('__') ? __('login.username') : 'Username or Email'; ?></label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password"><?php echo function_exists('__') ? __('login.password') : 'Password'; ?></label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-block"><?php echo function_exists('__') ? __('login.submit') : 'Login'; ?></button>
            </div>
        </form>

        <p class="auth-links">
            <?php echo function_exists('__') ? __('login.no_account') : "Don't have an account?"; ?>
            <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/register">
                <?php echo function_exists('__') ? __('login.register_link') : 'Register'; ?>
            </a><br>
            <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/forgot-password">
                <?php echo function_exists('__') ? __('login.forgot') : 'Forgot password?'; ?>
            </a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
