<?php
$title = 'Ajustes';
include __DIR__ . '/../partials/header.php';

$appUrl = rtrim($_ENV['APP_URL'] ?? getenv('APP_URL') ?: 'http://localhost:8080', '/');
?>

<div class="container" style="max-width: 640px; margin: 2rem auto; padding: 0 1rem;">
    <h1>Ajustes</h1>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success" style="background:#d4edda;color:#155724;padding:1rem;border-radius:8px;margin-bottom:1rem;">
            <?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-error" style="background:#f8d7da;color:#721c24;padding:1rem;border-radius:8px;margin-bottom:1rem;">
            <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <!-- Perfil -->
    <section style="background:#fff;border-radius:12px;padding:1.5rem;margin-bottom:1.5rem;box-shadow:0 2px 12px rgba(0,0,0,.08);">
        <h2 style="margin-top:0;">Perfil</h2>
        <form method="POST" action="<?php echo $appUrl; ?>/settings">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="action" value="update_profile">

            <label style="display:block;margin:.75rem 0 .25rem;">Nombre de usuario</label>
            <input type="text" name="username" required
                   value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>"
                   style="width:100%;padding:.6rem;border:1px solid #ccc;border-radius:8px;">

            <label style="display:block;margin:.75rem 0 .25rem;">Email</label>
            <input type="email" name="email" required
                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                   style="width:100%;padding:.6rem;border:1px solid #ccc;border-radius:8px;">

            <button type="submit" class="btn btn-primary" style="margin-top:1rem;">Guardar perfil</button>
        </form>
    </section>

    <!-- Contraseña -->
    <section style="background:#fff;border-radius:12px;padding:1.5rem;margin-bottom:1.5rem;box-shadow:0 2px 12px rgba(0,0,0,.08);">
        <h2 style="margin-top:0;">Cambiar contraseña</h2>
        <form method="POST" action="<?php echo $appUrl; ?>/settings">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="action" value="change_password">

            <label style="display:block;margin:.75rem 0 .25rem;">Contraseña actual</label>
            <input type="password" name="current_password" required
                   style="width:100%;padding:.6rem;border:1px solid #ccc;border-radius:8px;">

            <label style="display:block;margin:.75rem 0 .25rem;">Nueva contraseña</label>
            <input type="password" name="new_password" required minlength="8"
                   style="width:100%;padding:.6rem;border:1px solid #ccc;border-radius:8px;">

            <label style="display:block;margin:.75rem 0 .25rem;">Confirmar nueva contraseña</label>
            <input type="password" name="confirm_password" required minlength="8"
                   style="width:100%;padding:.6rem;border:1px solid #ccc;border-radius:8px;">

            <button type="submit" class="btn btn-primary" style="margin-top:1rem;">Cambiar contraseña</button>
        </form>
    </section>

    <!-- Notificaciones -->
    <section style="background:#fff;border-radius:12px;padding:1.5rem;margin-bottom:1.5rem;box-shadow:0 2px 12px rgba(0,0,0,.08);">
        <h2 style="margin-top:0;">Notificaciones</h2>
        <form method="POST" action="<?php echo $appUrl; ?>/settings">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="action" value="update_notifications">

            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                <input type="checkbox" name="notify_comments" value="1"
                    <?php echo !empty($user['notify_comments']) ? 'checked' : ''; ?>>
                Recibir email cuando alguien comente mis fotos
            </label>

            <button type="submit" class="btn btn-primary" style="margin-top:1rem;">Guardar preferencias</button>
        </form>
    </section>

    <p><a href="<?php echo $appUrl; ?>/profile">← Volver al perfil</a></p>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
