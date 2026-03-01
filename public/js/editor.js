/**
 * Editor JavaScript - Vista previa en vivo y GIF animado
 * BONUS 2: Live preview con drag & drop de stickers
 * BONUS 5: Generación de GIF animados
 */

// Estado del editor
const EditorState = {
    stream: null,
    selectedSticker: null,
    videoElement: null,
    canvasElement: null,
    previewCanvas: null,
    stickerPosition: { x: 50, y: 50 }, // Posición en porcentaje
    stickerSize: 100, // Tamaño en píxeles
    isDragging: false,
    gifFrames: [],
    isRecordingGif: false,
    gifInterval: null
};

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    initializeWebcam();
    initializeStickerSelection();
    initializeCaptureButton();
    initializeUploadButton();
    initializeDeleteButtons();
    initializeGifControls();
});

/**
 * Inicializar webcam
 */
async function initializeWebcam() {
    EditorState.videoElement = document.getElementById('webcam');
    EditorState.canvasElement = document.getElementById('capture-canvas');
    EditorState.previewCanvas = document.getElementById('preview-canvas');
    
    if (!EditorState.videoElement) return;
    
    try {
        EditorState.stream = await navigator.mediaDevices.getUserMedia({
            video: { width: 640, height: 480 },
            audio: false
        });
        
        EditorState.videoElement.srcObject = EditorState.stream;
        EditorState.videoElement.play();
        
        // Iniciar preview en tiempo real cuando el video esté listo
        EditorState.videoElement.addEventListener('loadedmetadata', () => {
            startLivePreview();
        });
        
        showNotification('Webcam iniciada correctamente', 'success');
    } catch (error) {
        console.error('Error al acceder a la webcam:', error);
        showNotification('No se pudo acceder a la webcam. Por favor, permite el acceso o usa la opción de subir archivo.', 'error');
        
        // Mostrar opción de subida de archivo
        document.querySelector('.upload-section').style.display = 'block';
    }
}

/**
 * BONUS 2: Vista previa en vivo con sticker superpuesto
 */
function startLivePreview() {
    const ctx = EditorState.previewCanvas.getContext('2d');
    EditorState.previewCanvas.width = EditorState.videoElement.videoWidth;
    EditorState.previewCanvas.height = EditorState.videoElement.videoHeight;
    
    function drawPreview() {
        if (!EditorState.videoElement.paused && !EditorState.videoElement.ended) {
            // Dibujar video
            ctx.drawImage(
                EditorState.videoElement,
                0, 0,
                EditorState.previewCanvas.width,
                EditorState.previewCanvas.height
            );
            
            // Dibujar sticker si está seleccionado
            if (EditorState.selectedSticker) {
                drawStickerOnCanvas(ctx, EditorState.previewCanvas.width, EditorState.previewCanvas.height);
            }
        }
        
        requestAnimationFrame(drawPreview);
    }
    
    drawPreview();
    initializeDragAndDrop();
}

/**
 * BONUS 2: Drag & Drop para posicionar stickers
 */
function initializeDragAndDrop() {
    const canvas = EditorState.previewCanvas;
    
    canvas.addEventListener('mousedown', startDrag);
    canvas.addEventListener('mousemove', drag);
    canvas.addEventListener('mouseup', stopDrag);
    canvas.addEventListener('mouseleave', stopDrag);
    
    // Soporte para touch (móviles)
    canvas.addEventListener('touchstart', (e) => {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousedown', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    });
    
    canvas.addEventListener('touchmove', (e) => {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousemove', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    });
    
    canvas.addEventListener('touchend', (e) => {
        e.preventDefault();
        const mouseEvent = new MouseEvent('mouseup', {});
        canvas.dispatchEvent(mouseEvent);
    });
}

function startDrag(e) {
    if (!EditorState.selectedSticker) return;
    
    const rect = EditorState.previewCanvas.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    
    // Verificar si el click está sobre el sticker
    const stickerX = EditorState.stickerPosition.x;
    const stickerY = EditorState.stickerPosition.y;
    const stickerWidth = (EditorState.stickerSize / EditorState.previewCanvas.width) * 100;
    
    if (Math.abs(x - stickerX) < stickerWidth / 2 && 
        Math.abs(y - stickerY) < stickerWidth / 2) {
        EditorState.isDragging = true;
        EditorState.previewCanvas.style.cursor = 'grabbing';
    }
}

function drag(e) {
    if (!EditorState.isDragging) return;
    
    const rect = EditorState.previewCanvas.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    
    // Limitar dentro del canvas
    EditorState.stickerPosition.x = Math.max(10, Math.min(90, x));
    EditorState.stickerPosition.y = Math.max(10, Math.min(90, y));
}

function stopDrag() {
    EditorState.isDragging = false;
    if (EditorState.previewCanvas) {
        EditorState.previewCanvas.style.cursor = EditorState.selectedSticker ? 'grab' : 'default';
    }
}

function drawStickerOnCanvas(ctx, width, height) {
    const stickerImg = document.querySelector(`.sticker-item.selected img`);
    if (!stickerImg) return;
    
    const x = (EditorState.stickerPosition.x / 100) * width - EditorState.stickerSize / 2;
    const y = (EditorState.stickerPosition.y / 100) * height - EditorState.stickerSize / 2;
    
    ctx.drawImage(stickerImg, x, y, EditorState.stickerSize, EditorState.stickerSize);
}

/**
 * Selección de stickers
 */
function initializeStickerSelection() {
    const stickerItems = document.querySelectorAll('.sticker-item');
    
    stickerItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remover selección anterior
            document.querySelectorAll('.sticker-item').forEach(s => s.classList.remove('selected'));
            
            // Seleccionar nuevo sticker
            this.classList.add('selected');
            EditorState.selectedSticker = this.dataset.sticker;
            
            // Habilitar botón de captura
            const captureBtn = document.getElementById('capture-btn');
            if (captureBtn) {
                captureBtn.disabled = false;
            }
            
            // Cambiar cursor del canvas
            if (EditorState.previewCanvas) {
                EditorState.previewCanvas.style.cursor = 'grab';
            }
            
            showNotification('Sticker seleccionado. ¡Puedes arrastrarlo para posicionarlo!', 'success');
        });
    });
    
    // Control de tamaño del sticker
    const sizeSlider = document.getElementById('sticker-size');
    if (sizeSlider) {
        sizeSlider.addEventListener('input', function() {
            EditorState.stickerSize = parseInt(this.value);
            document.getElementById('size-value').textContent = this.value + 'px';
        });
    }
}

/**
 * Captura de foto con sticker
 */
function initializeCaptureButton() {
    const captureBtn = document.getElementById('capture-btn');
    if (!captureBtn) return;
    
    captureBtn.addEventListener('click', async function() {
        if (!EditorState.selectedSticker) {
            showNotification('Por favor, selecciona un sticker primero', 'error');
            return;
        }
        
        this.disabled = true;
        this.textContent = 'Capturando...';
        
        try {
            // Capturar frame actual del canvas de preview
            const imageData = EditorState.previewCanvas.toDataURL('image/png');
            
            // Enviar al servidor
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;
            
            const response = await fetch('/editor/capture', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `image=${encodeURIComponent(imageData)}&sticker=${EditorState.selectedSticker}&csrf_token=${csrfToken}`
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('¡Foto capturada exitosamente!', 'success');
                addImageToGallery(result.filename, result.url);
            } else {
                showNotification(result.message || 'Error al capturar foto', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error al capturar foto', 'error');
        } finally {
            this.disabled = false;
            this.textContent = 'Capturar Foto';
        }
    });
}

/**
 * Subida de archivo
 */
function initializeUploadButton() {
    const uploadForm = document.getElementById('upload-form');
    if (!uploadForm) return;
    
    uploadForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!EditorState.selectedSticker) {
            showNotification('Por favor, selecciona un sticker primero', 'error');
            return;
        }
        
        const fileInput = document.getElementById('file-upload');
        if (!fileInput.files.length) {
            showNotification('Por favor, selecciona una imagen', 'error');
            return;
        }
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Subiendo...';
        
        try {
            const response = await fetch('/editor/upload', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('¡Imagen subida exitosamente!', 'success');
                addImageToGallery(result.filename, result.url);
                fileInput.value = '';
            } else {
                showNotification(result.message || 'Error al subir imagen', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error al subir imagen', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Subir Imagen';
        }
    });
}

/**
 * BONUS 5: Generación de GIF Animado
 */
function initializeGifControls() {
    const startGifBtn = document.getElementById('start-gif-btn');
    const stopGifBtn = document.getElementById('stop-gif-btn');
    
    if (startGifBtn) {
        startGifBtn.addEventListener('click', startGifRecording);
    }
    
    if (stopGifBtn) {
        stopGifBtn.addEventListener('click', stopGifRecording);
    }
}

function startGifRecording() {
    if (!EditorState.selectedSticker) {
        showNotification('Por favor, selecciona un sticker primero', 'error');
        return;
    }
    
    EditorState.isRecordingGif = true;
    EditorState.gifFrames = [];
    
    const startBtn = document.getElementById('start-gif-btn');
    const stopBtn = document.getElementById('stop-gif-btn');
    
    startBtn.disabled = true;
    stopBtn.disabled = false;
    
    showNotification('Grabando GIF... (máximo 3 segundos)', 'info');
    
    // Capturar frames cada 100ms (10 fps)
    let frameCount = 0;
    const maxFrames = 30; // 3 segundos a 10 fps
    
    EditorState.gifInterval = setInterval(() => {
        if (frameCount >= maxFrames) {
            stopGifRecording();
            return;
        }
        
        // Capturar frame actual
        const frameData = EditorState.previewCanvas.toDataURL('image/png');
        EditorState.gifFrames.push(frameData);
        frameCount++;
        
        // Actualizar progreso
        const progress = Math.round((frameCount / maxFrames) * 100);
        document.getElementById('gif-progress').textContent = `${progress}%`;
    }, 100);
}

async function stopGifRecording() {
    EditorState.isRecordingGif = false;
    
    if (EditorState.gifInterval) {
        clearInterval(EditorState.gifInterval);
        EditorState.gifInterval = null;
    }
    
    const startBtn = document.getElementById('start-gif-btn');
    const stopBtn = document.getElementById('stop-gif-btn');
    
    startBtn.disabled = false;
    stopBtn.disabled = true;
    
    if (EditorState.gifFrames.length < 5) {
        showNotification('Se necesitan al menos 5 frames para crear un GIF', 'error');
        return;
    }
    
    showNotification('Procesando GIF...', 'info');
    
    try {
        const csrfToken = document.querySelector('input[name="csrf_token"]').value;
        
        const response = await fetch('/editor/create-gif', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                frames: EditorState.gifFrames,
                sticker: EditorState.selectedSticker,
                csrf_token: csrfToken
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('¡GIF creado exitosamente!', 'success');
            addImageToGallery(result.filename, result.url);
        } else {
            showNotification(result.message || 'Error al crear GIF', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al crear GIF', 'error');
    } finally {
        EditorState.gifFrames = [];
        document.getElementById('gif-progress').textContent = '0%';
    }
}

/**
 * Eliminar imágenes
 */
function initializeDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            
            if (!confirm('¿Estás seguro de que quieres eliminar esta imagen?')) {
                return;
            }
            
            const imageId = this.dataset.imageId;
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;
            
            try {
                const response = await fetch('/editor/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `image_id=${imageId}&csrf_token=${csrfToken}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Remover elemento del DOM
                    this.closest('.thumbnail-item').remove();
                    showNotification('Imagen eliminada', 'success');
                } else {
                    showNotification('Error al eliminar imagen', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al eliminar imagen', 'error');
            }
        });
    });
}

/**
 * Agregar imagen a la galería de miniaturas
 */
function addImageToGallery(filename, url) {
    const gallery = document.querySelector('.thumbnails-grid');
    if (!gallery) return;
    
    const thumbnail = document.createElement('div');
    thumbnail.className = 'thumbnail-item';
    thumbnail.innerHTML = `
        <img src="${url}" alt="Foto capturada">
        <button class="btn-delete" data-image-id="new">
            <span class="delete-icon">🗑️</span>
        </button>
    `;
    
    gallery.prepend(thumbnail);
    
    // Re-inicializar event listeners
    initializeDeleteButtons();
}

/**
 * Función auxiliar para notificaciones
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => notification.classList.add('show'), 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Limpiar stream al salir de la página
window.addEventListener('beforeunload', () => {
    if (EditorState.stream) {
        EditorState.stream.getTracks().forEach(track => track.stop());
    }
});
