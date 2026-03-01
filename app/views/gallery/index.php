<?php $title = 'Galería'; include __DIR__ . '/../partials/header.php'; ?>

<div class="gallery-container">
    <h1>Galería Pública 📸</h1>
    <p class="subtitle">Explora las creaciones de nuestra comunidad</p>
    
    <div class="gallery-grid">
        <?php foreach ($images as $image): ?>
            <div class="gallery-item" id="image-<?php echo $image['id']; ?>">
                <img src="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/uploads/images/<?php echo htmlspecialchars($image['filename']); ?>" 
                     alt="Foto de <?php echo htmlspecialchars($image['username']); ?>">
                
                <div class="gallery-item-footer">
                    <div class="user-info">
                        <strong><?php echo htmlspecialchars($image['username']); ?></strong>
                        <span class="date"><?php echo date('d M Y', strtotime($image['created_at'])); ?></span>
                    </div>
                    
                    <div class="actions">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button class="btn-like <?php echo in_array($image['id'], $userLikes) ? 'liked' : ''; ?>" 
                                    data-image-id="<?php echo $image['id']; ?>">
                                <span class="like-icon"><?php echo in_array($image['id'], $userLikes) ? '❤️' : '🤍'; ?></span>
                                <span class="like-count"><?php echo $image['like_count']; ?></span>
                            </button>
                        <?php else: ?>
                            <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/login" class="btn-like">
                                <span class="like-icon">🤍</span>
                                <span class="like-count"><?php echo $image['like_count']; ?></span>
                            </a>
                        <?php endif; ?>
                        
                        <button class="btn-share" 
                                data-image-id="<?php echo $image['id']; ?>" 
                                data-filename="<?php echo htmlspecialchars($image['filename']); ?>">
                            🔗 Compartir
                        </button>
                    </div>
                    
                    <div class="comments-section">
                        <div class="comment-count"><?php echo $image['comment_count']; ?> comentarios</div>
                        
                        <div class="comments-list">
                            <?php if (isset($comments[$image['id']])): ?>
                                <?php foreach ($comments[$image['id']] as $comment): ?>
                                    <div class="comment">
                                        <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                                        <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                                        <span class="comment-date"><?php echo date('d M Y H:i', strtotime($comment['created_at'])); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form class="comment-form" data-image-id="<?php echo $image['id']; ?>">
                                <textarea name="comment" 
                                          placeholder="Añade un comentario..." 
                                          maxlength="500" 
                                          rows="2" 
                                          required></textarea>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <button type="submit" class="btn btn-secondary">Comentar</button>
                            </form>
                        <?php else: ?>
                            <p class="login-prompt">
                                <a href="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/login">Inicia sesión</a> para comentar
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Sentinel para scroll infinito -->
    <div class="gallery-sentinel"></div>
    
    <!-- Paginación tradicional (fallback si JS está deshabilitado) -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="btn">&laquo; Anterior</a>
            <?php endif; ?>
            
            <span class="page-info">Página <?php echo $page; ?> de <?php echo $totalPages; ?></span>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="btn">Siguiente &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.gallery-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.subtitle {
    text-align: center;
    color: var(--gray);
    margin-bottom: 2rem;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.gallery-item {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.gallery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
}

.gallery-item img {
    width: 100%;
    height: 350px;
    object-fit: cover;
}

.gallery-item-footer {
    padding: 1.5rem;
}

.user-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.date {
    color: var(--gray);
    font-size: 0.9rem;
}

.actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.btn-like, .btn-share {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: 2px solid var(--light);
    border-radius: 20px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-like:hover, .btn-share:hover {
    border-color: var(--primary-color);
    transform: scale(1.05);
}

.btn-like.liked {
    border-color: var(--danger-color);
    background: #ffe6e6;
}

.comments-section {
    margin-top: 1rem;
    border-top: 1px solid var(--light);
    padding-top: 1rem;
}

.comment-count {
    font-weight: bold;
    margin-bottom: 1rem;
    color: var(--dark);
}

.comments-list {
    max-height: 200px;
    overflow-y: auto;
    margin-bottom: 1rem;
}

.comment {
    padding: 0.75rem;
    background: var(--light);
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.comment strong {
    color: var(--primary-color);
}

.comment p {
    margin: 0.5rem 0;
}

.comment-date {
    font-size: 0.8rem;
    color: var(--gray);
}

.comment-form textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--light);
    border-radius: 8px;
    resize: vertical;
    min-height: 60px;
    font-family: inherit;
}

.comment-form button {
    margin-top: 0.5rem;
    width: 100%;
}

.login-prompt {
    text-align: center;
    padding: 1rem;
    background: var(--light);
    border-radius: 8px;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2rem;
    margin-top: 2rem;
}

.page-info {
    font-weight: bold;
}

/* Scroll infinito */
.loading-indicator {
    text-align: center;
    padding: 2rem;
}

.end-message {
    text-align: center;
    padding: 2rem;
    color: var(--gray);
    font-size: 1.2rem;
}

/* Modal de compartir */
.share-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    position: relative;
}

.modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
}

.share-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-top: 1.5rem;
}

.share-btn {
    padding: 1rem;
    border-radius: 8px;
    text-decoration: none;
    text-align: center;
    font-weight: bold;
    transition: transform 0.3s ease;
}

.share-btn:hover {
    transform: scale(1.05);
}

.share-btn.facebook { background: #1877f2; color: white; }
.share-btn.twitter { background: #1da1f2; color: white; }
.share-btn.whatsapp { background: #25d366; color: white; }
.share-btn.copy { background: var(--gray); color: white; border: none; cursor: pointer; }

/* Notificaciones */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 10000;
    opacity: 0;
    transform: translateX(400px);
    transition: all 0.3s ease;
}

.notification.show {
    opacity: 1;
    transform: translateX(0);
}

.notification-success { background: var(--success-color); color: white; }
.notification-error { background: var(--danger-color); color: white; }
.notification-info { background: var(--primary-color); color: white; }

@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: 1fr;
    }
    
    .share-options {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/js/gallery.js"></script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
