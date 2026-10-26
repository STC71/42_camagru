<?php
$title = 'Mi perfil';
include __DIR__ . '/../partials/header.php';

$appUrl = rtrim($_ENV['APP_URL'] ?? getenv('APP_URL') ?: 'http://localhost:8080', '/');
?>

<div class="container" style="max-width: 1000px; margin: 2rem auto; padding: 0 1rem;">
    <h1>Mi perfil</h1>

    <?php if (!empty($user)): ?>
        <div class="profile-card" style="background:#fff;border-radius:12px;padding:1.5rem;margin-bottom:2rem;box-shadow:0 2px 12px rgba(0,0,0,.08);">
            <p><strong>Usuario:</strong> <?php echo htmlspecialchars($user['username'] ?? ''); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
            <p>
                <a class="btn btn-secondary" href="<?php echo $appUrl; ?>/settings">⚙️ Ajustes</a>
                <a class="btn btn-primary" href="<?php echo $appUrl; ?>/editor">📷 Ir al editor</a>
            </p>
        </div>
    <?php endif; ?>

    <h2>Mis fotos</h2>

    <?php if (empty($images)): ?>
        <p style="color:#666;">Aún no has capturado ni subido ninguna foto.
            <a href="<?php echo $appUrl; ?>/editor">Crear la primera</a>
        </p>
    <?php else: ?>
        <div class="my-images-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;">
            <?php foreach ($images as $image): ?>
                <div class="my-image-item" style="background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);">
                    <img src="<?php echo $appUrl; ?>/uploads/images/<?php echo htmlspecialchars($image['filename']); ?>"
                         alt="Mi foto"
                         style="width:100%;height:220px;object-fit:cover;display:block;">
                    <div style="padding:.75rem;font-size:.9rem;color:#555;">
                        <?php echo date('d M Y H:i', strtotime($image['created_at'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
