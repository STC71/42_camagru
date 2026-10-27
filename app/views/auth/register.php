<?php $title = function_exists('__') ? __('register.title') : 'Register'; include __DIR__ . '/../partials/header.php'; ?>

<div class="auth-wrap">
    <div class="card auth-card">
        <h1><?php echo function_exists('__') ? __('register.title') : 'Create account'; ?></h1>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

            <div class="form-group">
                <label for="username"><?php echo function_exists('__') ? __('register.username') : 'Username'; ?></label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>

            <div class="form-group">
                <label for="email"><?php echo function_exists('__') ? __('register.email') : 'Email'; ?></label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password"><?php echo function_exists('__') ? __('register.password') : 'Password'; ?></label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
            </div>

            <div class="form-group">
                <label for="confirm_password"><?php echo function_exists('__') ? __('register.confirm') : 'Confirm password'; ?></label>
                <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-block"><?php echo function_exists('__') ? __('register.submit') : 'Register'; ?></button>
            </div>
        </form>

        <p class="auth-links">
            <?php echo function_exists('__') ? __('register.have_account') : 'Already have an account?'; ?>
            <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/login">
                <?php echo function_exists('__') ? __('nav.login') : 'Login'; ?>
            </a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
