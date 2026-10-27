<?php
$title = function_exists('__') ? __('nav.settings') : 'Settings';
include __DIR__ . '/../partials/header.php';
$appUrl = rtrim($_ENV['APP_URL'] ?? getenv('APP_URL') ?: 'http://localhost:8080', '/');
?>
<div class="auth-wrap">
<div style="max-width:640px;width:100%;">
    <h1 style="text-align:center;color:#fff;margin-bottom:1.25rem;"><?php echo function_exists('__') ? __('nav.settings') : 'Ajustes'; ?></h1>
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash flash-success"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="flash flash-error"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>
    <section class="card auth-card" style="max-width:100%;margin-bottom:1.25rem;">
        <h2 style="margin-top:0;"><?php echo function_exists('__') ? __('settings.profile') : 'Perfil'; ?></h2>
        <form method="POST" action="<?php echo $appUrl; ?>/settings">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="action" value="update_profile">
            <div class="form-group">
                <label><?php echo function_exists('__') ? __('register.username') : 'Usuario'; ?></label>
                <input type="text" name="username" required value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label><?php echo function_exists('__') ? __('register.email') : 'Email'; ?></label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
            </div>
            <button type="submit" class="btn btn-block"><?php echo function_exists('__') ? __('settings.save_profile') : 'Guardar perfil'; ?></button>
        </form>
    </section>
    <section class="card auth-card" style="max-width:100%;margin-bottom:1.25rem;">
        <h2 style="margin-top:0;"><?php echo function_exists('__') ? __('settings.password') : 'Cambiar contraseña'; ?></h2>
        <form method="POST" action="<?php echo $appUrl; ?>/settings">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="action" value="change_password">
            <div class="form-group">
                <label><?php echo function_exists('__') ? __('settings.current_password') : 'Contraseña actual'; ?></label>
                <input type="password" name="current_password" required>
            </div>
            <div class="form-group">
                <label><?php echo function_exists('__') ? __('settings.new_password') : 'Nueva contraseña'; ?></label>
                <input type="password" name="new_password" required minlength="8">
            </div>
            <div class="form-group">
                <label><?php echo function_exists('__') ? __('settings.confirm_password') : 'Confirmar'; ?></label>
                <input type="password" name="confirm_password" required minlength="8">
            </div>
            <button type="submit" class="btn btn-block"><?php echo function_exists('__') ? __('settings.change_password') : 'Cambiar contraseña'; ?></button>
        </form>
    </section>
    <section class="card auth-card" style="max-width:100%;">
        <h2 style="margin-top:0;"><?php echo function_exists('__') ? __('settings.notifications') : 'Notificaciones'; ?></h2>
        <form method="POST" action="<?php echo $appUrl; ?>/settings">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="action" value="update_notifications">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                <input type="checkbox" name="notify_comments" value="1" <?php echo !empty($user['notify_comments']) ? 'checked' : ''; ?>>
                <?php echo function_exists('__') ? __('settings.notify_comments') : 'Email cuando comenten mis fotos'; ?>
            </label>
            <button type="submit" class="btn btn-block" style="margin-top:1rem;"><?php echo function_exists('__') ? __('settings.save_prefs') : 'Guardar preferencias'; ?></button>
        </form>
    </section>
</div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
