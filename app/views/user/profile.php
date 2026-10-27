<?php
$title = function_exists('__') ? __('nav.profile') : 'Profile';
include __DIR__ . '/../partials/header.php';
$appUrl = rtrim($_ENV['APP_URL'] ?? getenv('APP_URL') ?: 'http://localhost:8080', '/');
?>
<div class="container profile-page" style="max-width: 1000px; margin: 2rem auto; padding: 0 1rem;">
    <h1 style="color:#fff;text-align:center;"><?php echo function_exists('__') ? __('nav.profile') : 'Mi perfil'; ?></h1>
    <?php if (!empty($user)): ?>
        <div class="card" style="margin:1.25rem auto 2rem;max-width:560px;">
            <p><strong><?php echo function_exists('__') ? __('register.username') : 'Usuario'; ?>:</strong>
                <?php echo htmlspecialchars($user['username'] ?? ''); ?></p>
            <p><strong><?php echo function_exists('__') ? __('register.email') : 'Email'; ?>:</strong>
                <?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
            <p style="margin-top:1rem;">
                <a class="btn btn-secondary" href="<?php echo $appUrl; ?>/settings">⚙️ <?php echo function_exists('__') ? __('nav.settings') : 'Ajustes'; ?></a>
                <a class="btn" href="<?php echo $appUrl; ?>/editor">📷 <?php echo function_exists('__') ? __('nav.editor') : 'Editor'; ?></a>
            </p>
        </div>
    <?php endif; ?>
    <h2 style="color:#fff;text-align:center;"><?php echo function_exists('__') ? __('profile.my_photos') : 'Mis fotos'; ?></h2>
    <?php if (empty($images)): ?>
        <div class="gallery-empty" style="margin:1rem auto;max-width:480px;">
            <p><?php echo function_exists('__') ? __('profile.no_photos') : 'Aún no has capturado ni subido ninguna foto.'; ?></p>
            <a class="btn" href="<?php echo $appUrl; ?>/editor"><?php echo function_exists('__') ? __('profile.create_first') : 'Crear la primera'; ?></a>
        </div>
    <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;margin-top:1rem;">
            <?php foreach ($images as $image): ?>
                <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.08);">
                    <img src="<?php echo $appUrl; ?>/uploads/images/<?php echo htmlspecialchars($image['filename']); ?>"
                         alt="" style="width:100%;height:220px;object-fit:cover;display:block;">
                    <div style="padding:.75rem;font-size:.9rem;color:#555;"><?php echo date('d M Y H:i', strtotime($image['created_at'])); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
