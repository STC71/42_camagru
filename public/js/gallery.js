/**
 * Gallery JavaScript - Características AJAX y Scroll Infinito
 * Maneja likes, comentarios y carga dinámica de imágenes
 */

// Estado de la galería
const GalleryState = {
    currentPage: 1,
    loading: false,
    hasMore: true,
    observerInitialized: false
};

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    initializeLikeButtons();
    initializeCommentForms();
    initializeInfiniteScroll();
    initializeShareButtons();
});

/**
 * BONUS 1: AJAX - Sistema de Likes sin recargar página
 */
function initializeLikeButtons() {
    const likeButtons = document.querySelectorAll('.btn-like');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const imageId = this.dataset.imageId;
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;
            
            // Deshabilitar botón temporalmente
            this.disabled = true;
            
            // Realizar petición AJAX
            ajax('/gallery/like', 'POST', {
                image_id: imageId,
                csrf_token: csrfToken
            }, (error, response) => {
                this.disabled = false;
                
                if (error || !response.success) {
                    showNotification('Error al procesar like', 'error');
                    return;
                }
                
                // Actualizar UI
                const likeCount = this.querySelector('.like-count');
                likeCount.textContent = response.like_count;
                
                // Cambiar icono según estado
                const icon = this.querySelector('.like-icon');
                if (response.has_liked) {
                    icon.textContent = '❤️';
                    this.classList.add('liked');
                    showNotification('¡Te gusta esta foto!', 'success');
                } else {
                    icon.textContent = '🤍';
                    this.classList.remove('liked');
                }
            });
        });
    });
}

/**
 * BONUS 1: AJAX - Sistema de Comentarios dinámico
 */
function initializeCommentForms() {
    const commentForms = document.querySelectorAll('.comment-form');
    
    commentForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const imageId = this.dataset.imageId;
            const commentInput = this.querySelector('textarea[name="comment"]');
            const comment = commentInput.value.trim();
            const csrfToken = this.querySelector('input[name="csrf_token"]').value;
            
            if (!comment) {
                showNotification('El comentario no puede estar vacío', 'error');
                return;
            }
            
            // Deshabilitar formulario temporalmente
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';
            
            // Realizar petición AJAX
            ajax('/gallery/comment', 'POST', {
                image_id: imageId,
                comment: comment,
                csrf_token: csrfToken
            }, (error, response) => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Comentar';
                
                if (error || !response.success) {
                    showNotification(response.message || 'Error al enviar comentario', 'error');
                    return;
                }
                
                // Limpiar input
                commentInput.value = '';
                
                // Agregar comentario a la lista
                addCommentToList(imageId, response.comment);
                
                // Actualizar contador
                updateCommentCount(imageId);
                
                showNotification('¡Comentario añadido!', 'success');
            });
        });
    });
}

/**
 * BONUS 3: Scroll Infinito - Carga automática de imágenes
 */
function initializeInfiniteScroll() {
    // Verificar si ya hay un observador
    if (GalleryState.observerInitialized) return;
    
    const sentinel = document.querySelector('.gallery-sentinel');
    if (!sentinel) {
        console.log('Sentinel no encontrado, creando...');
        createSentinel();
    }
    
    // Crear Intersection Observer
    const options = {
        root: null,
        rootMargin: '100px',
        threshold: 0.1
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !GalleryState.loading && GalleryState.hasMore) {
                loadMoreImages();
            }
        });
    }, options);
    
    // Observar el elemento sentinel
    const sentinelElement = document.querySelector('.gallery-sentinel');
    if (sentinelElement) {
        observer.observe(sentinelElement);
        GalleryState.observerInitialized = true;
    }
}

function createSentinel() {
    const galleryGrid = document.querySelector('.gallery-grid');
    if (!galleryGrid) return;
    
    const sentinel = document.createElement('div');
    sentinel.className = 'gallery-sentinel';
    sentinel.style.height = '1px';
    sentinel.style.width = '100%';
    
    // Insertar después de la galería
    galleryGrid.parentNode.insertBefore(sentinel, galleryGrid.nextSibling);
}

function loadMoreImages() {
    if (GalleryState.loading) return;
    
    GalleryState.loading = true;
    GalleryState.currentPage++;
    
    // Mostrar indicador de carga
    showLoadingIndicator();
    
    // Realizar petición AJAX
    const url = `/gallery?page=${GalleryState.currentPage}&ajax=1`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.images && data.images.length > 0) {
                appendImages(data.images);
                
                if (data.images.length < 5) {
                    GalleryState.hasMore = false;
                    showEndMessage();
                }
            } else {
                GalleryState.hasMore = false;
                showEndMessage();
            }
            
            GalleryState.loading = false;
            hideLoadingIndicator();
        })
        .catch(error => {
            console.error('Error al cargar más imágenes:', error);
            GalleryState.loading = false;
            hideLoadingIndicator();
            showNotification('Error al cargar más imágenes', 'error');
        });
}

function appendImages(images) {
    const galleryGrid = document.querySelector('.gallery-grid');
    if (!galleryGrid) return;
    
    images.forEach(image => {
        const imageCard = createImageCard(image);
        galleryGrid.appendChild(imageCard);
    });
    
    // Re-inicializar event listeners para nuevas imágenes
    initializeLikeButtons();
    initializeCommentForms();
    initializeShareButtons();
}

function createImageCard(image) {
    const card = document.createElement('div');
    card.className = 'gallery-item';
    card.innerHTML = `
        <img src="/uploads/images/${image.filename}" alt="Foto de ${image.username}">
        <div class="gallery-item-footer">
            <div class="user-info">
                <strong>${image.username}</strong>
                <span class="date">${formatDate(image.created_at)}</span>
            </div>
            <div class="actions">
                <button class="btn-like" data-image-id="${image.id}">
                    <span class="like-icon">${image.has_liked ? '❤️' : '🤍'}</span>
                    <span class="like-count">${image.like_count}</span>
                </button>
                <button class="btn-share" data-image-id="${image.id}" data-filename="${image.filename}">
                    🔗 Compartir
                </button>
            </div>
            <div class="comments-section">
                <div class="comment-count">${image.comment_count} comentarios</div>
                <form class="comment-form" data-image-id="${image.id}">
                    <textarea name="comment" placeholder="Añade un comentario..." maxlength="500"></textarea>
                    <input type="hidden" name="csrf_token" value="${getCsrfToken()}">
                    <button type="submit">Comentar</button>
                </form>
            </div>
        </div>
    `;
    
    return card;
}

/**
 * BONUS 4: Compartir en Redes Sociales
 */
function initializeShareButtons() {
    const shareButtons = document.querySelectorAll('.btn-share');
    
    shareButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            showShareModal(this.dataset.imageId, this.dataset.filename);
        });
    });
}

function showShareModal(imageId, filename) {
    const appUrl = window.location.origin;
    const imageUrl = `${appUrl}/uploads/images/${filename}`;
    const shareUrl = `${appUrl}/gallery#image-${imageId}`;
    
    const modal = document.createElement('div');
    modal.className = 'share-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <h3>Compartir Foto</h3>
            <div class="share-options">
                <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}" 
                   target="_blank" class="share-btn facebook">
                    📘 Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl)}&text=¡Mira esta foto!" 
                   target="_blank" class="share-btn twitter">
                    🐦 Twitter
                </a>
                <a href="https://wa.me/?text=¡Mira esta foto! ${encodeURIComponent(shareUrl)}" 
                   target="_blank" class="share-btn whatsapp">
                    💬 WhatsApp
                </a>
                <button class="share-btn copy" onclick="copyToClipboard('${shareUrl}')">
                    📋 Copiar enlace
                </button>
            </div>
            <button class="modal-close" onclick="this.parentElement.parentElement.remove()">✕</button>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Cerrar al hacer click fuera del modal
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('¡Enlace copiado!', 'success');
    }).catch(() => {
        showNotification('Error al copiar enlace', 'error');
    });
}

// Funciones auxiliares
function addCommentToList(imageId, comment) {
    const commentsList = document.querySelector(`[data-image-id="${imageId}"] .comments-list`);
    if (!commentsList) return;
    
    const commentElement = document.createElement('div');
    commentElement.className = 'comment';
    commentElement.innerHTML = `
        <strong>${comment.username}</strong>
        <p>${comment.comment}</p>
        <span class="comment-date">${formatDate(comment.created_at)}</span>
    `;
    
    commentsList.prepend(commentElement);
}

function updateCommentCount(imageId) {
    const countElement = document.querySelector(`[data-image-id="${imageId}"] .comment-count`);
    if (!countElement) return;
    
    const currentCount = parseInt(countElement.textContent);
    countElement.textContent = `${currentCount + 1} comentarios`;
}

function showLoadingIndicator() {
    let indicator = document.querySelector('.loading-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.className = 'loading-indicator';
        indicator.innerHTML = '<div class="spinner"></div><p>Cargando más imágenes...</p>';
        document.querySelector('.gallery-sentinel').before(indicator);
    }
    indicator.style.display = 'block';
}

function hideLoadingIndicator() {
    const indicator = document.querySelector('.loading-indicator');
    if (indicator) {
        indicator.style.display = 'none';
    }
}

function showEndMessage() {
    const message = document.createElement('div');
    message.className = 'end-message';
    message.textContent = '¡Has visto todas las fotos! 🎉';
    document.querySelector('.gallery-sentinel').before(message);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    
    // Menos de un minuto
    if (diff < 60000) {
        return 'Hace un momento';
    }
    // Menos de una hora
    if (diff < 3600000) {
        const minutes = Math.floor(diff / 60000);
        return `Hace ${minutes} ${minutes === 1 ? 'minuto' : 'minutos'}`;
    }
    // Menos de un día
    if (diff < 86400000) {
        const hours = Math.floor(diff / 3600000);
        return `Hace ${hours} ${hours === 1 ? 'hora' : 'horas'}`;
    }
    // Más de un día
    return date.toLocaleDateString('es-ES', { 
        day: 'numeric', 
        month: 'short', 
        year: 'numeric' 
    });
}

function getCsrfToken() {
    const tokenInput = document.querySelector('input[name="csrf_token"]');
    return tokenInput ? tokenInput.value : '';
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Mostrar con animación
    setTimeout(() => notification.classList.add('show'), 10);
    
    // Ocultar después de 3 segundos
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
