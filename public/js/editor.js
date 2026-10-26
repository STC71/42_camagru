/**
 * Editor JavaScript - Vista previa en vivo, captura server-side y GIF
 *
 * Reglas importantes (subject 42):
 * - La fusión imagen + sticker se hace en el SERVIDOR (GD).
 * - El cliente solo envía la foto BASE (sin sticker) + posición/tamaño del sticker.
 * - Así no hay sticker duplicado y el resultado respeta el drag & drop.
 */

const EditorState = {
    stream: null,
    selectedSticker: null,
    videoElement: null,
    previewCanvas: null,   // #live-preview (vídeo + sticker para el usuario)
    captureCanvas: null,   // #capture-canvas (solo vídeo, oculto)
    stickerPosition: { x: 50, y: 50 }, // centro del sticker en % del canvas
    stickerSize: 100,      // tamaño en px del lado del sticker en el preview
    isDragging: false,
    gifFrames: [],
    isRecordingGif: false,
    gifInterval: null,
    previewRunning: false
};

document.addEventListener('DOMContentLoaded', function () {
    EditorState.videoElement = document.getElementById('webcam');
    EditorState.previewCanvas = document.getElementById('live-preview');
    EditorState.captureCanvas = document.getElementById('capture-canvas');

    initializeTabs();
    initializeStartCameraButton();
    initializeStickerSelection();
    initializeCaptureButton();
    initializeUploadButton();
    initializeDeleteButtons();
    initializeGifControls();
    initializeDragAndDrop();
});

function getCsrfToken() {
    const el = document.getElementById('csrf_token')
        || document.querySelector('input[name="csrf_token"]');
    return el ? el.value : '';
}

/**
 * Pestañas webcam / subir archivo
 */
function initializeTabs() {
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tab-btn').forEach(function (b) {
                b.classList.remove('active');
            });
            document.querySelectorAll('.tab-content').forEach(function (c) {
                c.classList.remove('active');
            });
            this.classList.add('active');
            const tab = document.getElementById('tab-' + this.dataset.tab);
            if (tab) {
                tab.classList.add('active');
            }
        });
    });
}

/**
 * Botón "Iniciar Cámara"
 */
function initializeStartCameraButton() {
    const startBtn = document.getElementById('start-camera');
    if (!startBtn) {
        // Si no hay botón, intentamos arrancar al cargar
        startWebcam();
        return;
    }
    startBtn.addEventListener('click', function () {
        startWebcam();
    });
}

async function startWebcam() {
    if (!EditorState.videoElement) {
        return;
    }
    try {
        if (EditorState.stream) {
            EditorState.stream.getTracks().forEach(function (t) {
                t.stop();
            });
        }
        EditorState.stream = await navigator.mediaDevices.getUserMedia({
            video: { width: 640, height: 480 },
            audio: false
        });
        EditorState.videoElement.srcObject = EditorState.stream;
        await EditorState.videoElement.play();

        EditorState.videoElement.addEventListener('loadedmetadata', function onMeta() {
            EditorState.videoElement.removeEventListener('loadedmetadata', onMeta);
            startLivePreview();
        });
        // Por si metadata ya estaba lista
        if (EditorState.videoElement.readyState >= 1) {
            startLivePreview();
        }

        const startBtn = document.getElementById('start-camera');
        if (startBtn) {
            startBtn.textContent = '🎥 Cámara activa';
            startBtn.disabled = true;
        }
        updateActionButtonsState();
        showNotification('Cámara iniciada', 'success');
    } catch (err) {
        console.error(err);
        showNotification('No se pudo acceder a la webcam', 'error');
    }
}

/**
 * Bucle de vista previa: vídeo + sticker (solo visual)
 */
function startLivePreview() {
    if (!EditorState.previewCanvas || !EditorState.videoElement) {
        return;
    }
    if (EditorState.previewRunning) {
        return;
    }
    EditorState.previewRunning = true;

    const w = EditorState.videoElement.videoWidth || 640;
    const h = EditorState.videoElement.videoHeight || 480;
    EditorState.previewCanvas.width = w;
    EditorState.previewCanvas.height = h;
    if (EditorState.captureCanvas) {
        EditorState.captureCanvas.width = w;
        EditorState.captureCanvas.height = h;
    }

    // Canvas encima del vídeo (el vídeo sigue dando el tamaño al contenedor)
    EditorState.previewCanvas.classList.add('active');
    EditorState.previewCanvas.style.display = 'block';
    EditorState.previewCanvas.style.pointerEvents = 'auto';
    EditorState.previewCanvas.style.zIndex = '2';
    // El vídeo no debe “comerse” los clics/drags del sticker
    EditorState.videoElement.style.pointerEvents = 'none';

    function draw() {
        if (!EditorState.previewRunning) {
            return;
        }
        const ctx = EditorState.previewCanvas.getContext('2d');
        const width = EditorState.previewCanvas.width;
        const height = EditorState.previewCanvas.height;

        if (EditorState.videoElement.readyState >= 2) {
            ctx.drawImage(EditorState.videoElement, 0, 0, width, height);
            if (EditorState.selectedSticker) {
                drawStickerOnCanvas(ctx, width, height);
            }
        }
        requestAnimationFrame(draw);
    }
    draw();
}

function drawStickerOnCanvas(ctx, width, height) {
    const stickerImg = document.querySelector('.sticker-item.selected img');
    if (!stickerImg || !stickerImg.complete) {
        return;
    }
    const size = EditorState.stickerSize;
    const x = (EditorState.stickerPosition.x / 100) * width - size / 2;
    const y = (EditorState.stickerPosition.y / 100) * height - size / 2;
    ctx.drawImage(stickerImg, x, y, size, size);
}

/**
 * Captura SOLO el frame de vídeo (sin sticker) en el canvas oculto
 */
function captureBaseFrameDataUrl(asJpeg) {
    if (!EditorState.captureCanvas || !EditorState.videoElement) {
        return null;
    }
    let w = EditorState.videoElement.videoWidth || EditorState.captureCanvas.width;
    let h = EditorState.videoElement.videoHeight || EditorState.captureCanvas.height;
    // Limitar resolución para no reventar post_max_size / memoria
    const maxW = asJpeg ? 480 : 640;
    if (w > maxW) {
        h = Math.round(h * (maxW / w));
        w = maxW;
    }
    EditorState.captureCanvas.width = w;
    EditorState.captureCanvas.height = h;
    const ctx = EditorState.captureCanvas.getContext('2d');
    ctx.drawImage(EditorState.videoElement, 0, 0, w, h);
    if (asJpeg) {
        return EditorState.captureCanvas.toDataURL('image/jpeg', 0.7);
    }
    return EditorState.captureCanvas.toDataURL('image/png');
}

/**
 * Drag & drop del sticker sobre el canvas de preview
 */
function initializeDragAndDrop() {
    const canvas = EditorState.previewCanvas || document.getElementById('live-preview');
    if (!canvas) {
        return;
    }

    // Clic = colocar sticker en ese punto; arrastrar = moverlo
    canvas.addEventListener('mousedown', function (e) {
        if (!EditorState.selectedSticker) {
            return;
        }
        EditorState.isDragging = true;
        canvas.style.cursor = 'grabbing';
        updateStickerPositionFromEvent(e, canvas);
        e.preventDefault();
    });
    window.addEventListener('mousemove', function (e) {
        if (!EditorState.isDragging) {
            return;
        }
        updateStickerPositionFromEvent(e, canvas);
    });
    window.addEventListener('mouseup', function () {
        EditorState.isDragging = false;
        if (canvas) {
            canvas.style.cursor = EditorState.selectedSticker ? 'grab' : 'default';
        }
    });

    // Táctil
    canvas.addEventListener('touchstart', function (e) {
        if (!EditorState.selectedSticker || !e.touches[0]) {
            return;
        }
        EditorState.isDragging = true;
        updateStickerPositionFromTouch(e.touches[0], canvas);
        e.preventDefault();
    }, { passive: false });
    canvas.addEventListener('touchmove', function (e) {
        if (!EditorState.isDragging || !e.touches[0]) {
            return;
        }
        updateStickerPositionFromTouch(e.touches[0], canvas);
        e.preventDefault();
    }, { passive: false });
    canvas.addEventListener('touchend', function () {
        EditorState.isDragging = false;
    });
}

function updateStickerPositionFromEvent(e, canvas) {
    const rect = canvas.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    EditorState.stickerPosition.x = Math.max(5, Math.min(95, x));
    EditorState.stickerPosition.y = Math.max(5, Math.min(95, y));
}

function updateStickerPositionFromTouch(touch, canvas) {
    const rect = canvas.getBoundingClientRect();
    const x = ((touch.clientX - rect.left) / rect.width) * 100;
    const y = ((touch.clientY - rect.top) / rect.height) * 100;
    EditorState.stickerPosition.x = Math.max(5, Math.min(95, x));
    EditorState.stickerPosition.y = Math.max(5, Math.min(95, y));
}

/**
 * Selección de stickers
 */
function initializeStickerSelection() {
    document.querySelectorAll('.sticker-item').forEach(function (item) {
        item.addEventListener('click', function () {
            document.querySelectorAll('.sticker-item').forEach(function (s) {
                s.classList.remove('selected');
            });
            this.classList.add('selected');
            EditorState.selectedSticker = this.dataset.sticker;

            const sizeControl = document.getElementById('sticker-size-control') || document.querySelector('.sticker-size-control');
            if (sizeControl) {
                sizeControl.style.display = 'block';
            }
            if (EditorState.previewCanvas) {
                EditorState.previewCanvas.style.cursor = 'grab';
            }
            updateActionButtonsState();
            showNotification('Sticker seleccionado. Arrástralo para posicionarlo.', 'success');
        });
    });

    const sizeSlider = document.getElementById('sticker-size');
    if (sizeSlider) {
        sizeSlider.addEventListener('input', function () {
            EditorState.stickerSize = parseInt(this.value, 10) || 100;
            const label = document.getElementById('size-value');
            if (label) {
                label.textContent = this.value + 'px';
            }
        });
    }
}

function updateActionButtonsState() {
    const canCapture = !!(EditorState.selectedSticker && EditorState.stream);
    ['capture-photo', 'start-gif'].forEach(function (id) {
        const btn = document.getElementById(id);
        if (btn) {
            btn.disabled = !canCapture;
        }
    });
}

/**
 * Captura: envía imagen BASE + datos del sticker al servidor
 */
function initializeCaptureButton() {
    const captureBtn = document.getElementById('capture-photo');
    if (!captureBtn) {
        return;
    }

    captureBtn.addEventListener('click', async function () {
        if (!EditorState.selectedSticker) {
            showNotification('Selecciona un sticker primero', 'error');
            return;
        }
        if (!EditorState.stream) {
            showNotification('Inicia la cámara primero', 'error');
            return;
        }

        const imageData = captureBaseFrameDataUrl();
        if (!imageData) {
            showNotification('No se pudo capturar el frame', 'error');
            return;
        }

        this.disabled = true;
        const originalText = this.textContent;
        this.textContent = 'Capturando...';

        try {
            const body = new URLSearchParams();
            body.set('image', imageData);
            body.set('sticker', EditorState.selectedSticker);
            body.set('sticker_x', String(EditorState.stickerPosition.x));
            body.set('sticker_y', String(EditorState.stickerPosition.y));
            body.set('sticker_size', String(EditorState.stickerSize));
            body.set('csrf_token', getCsrfToken());

            const response = await fetch('/editor/capture', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: body.toString()
            });
            if (response.status === 401) {
                showNotification('Sesión caducada. Vuelve a iniciar sesión.', 'error');
                window.location.href = '/login';
                return;
            }
            const result = await response.json();

            if (result.success) {
                showNotification('¡Foto capturada con éxito!', 'success');
                if (result.url) {
                    addImageToGallery(result.filename, result.url);
                }
            } else {
                showNotification(result.message || 'Error al capturar foto', 'error');
            }
        } catch (err) {
            console.error(err);
            showNotification('Error al capturar foto', 'error');
        } finally {
            this.disabled = false;
            this.textContent = originalText;
            updateActionButtonsState();
        }
    });
}

/**
 * Subida de archivo (0 o 1 sticker) — no tocamos la lógica del backend
 */
function initializeUploadButton() {
    const uploadForm = document.getElementById('upload-form');
    if (!uploadForm) {
        return;
    }

    uploadForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const fileInput = document.getElementById('file-input');
        if (!fileInput || !fileInput.files.length) {
            showNotification('Selecciona una imagen', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('image', fileInput.files[0]);
        formData.append('csrf_token', getCsrfToken());
        if (EditorState.selectedSticker) {
            formData.append('sticker', EditorState.selectedSticker);
            formData.append('sticker_x', String(EditorState.stickerPosition.x));
            formData.append('sticker_y', String(EditorState.stickerPosition.y));
            formData.append('sticker_size', String(EditorState.stickerSize));
        }

        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Subiendo...';
        }

        try {
            const response = await fetch('/editor/upload', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });
            const result = await response.json();
            if (result.success) {
                showNotification('¡Imagen subida con éxito!', 'success');
                if (result.url) {
                    addImageToGallery(result.filename, result.url);
                }
                fileInput.value = '';
            } else {
                showNotification(result.message || 'Error al subir imagen', 'error');
            }
        } catch (err) {
            console.error(err);
            showNotification('Error al subir imagen', 'error');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Subir Imagen';
            }
        }
    });
}

/**
 * GIF: frames BASE (sin sticker) + FormData (para que PHP rellene $_POST)
 */
function initializeGifControls() {
    const startBtn = document.getElementById('start-gif');
    const stopBtn = document.getElementById('stop-gif');
    if (startBtn) {
        startBtn.addEventListener('click', startGifRecording);
    }
    if (stopBtn) {
        stopBtn.addEventListener('click', stopGifRecording);
    }
}

function startGifRecording() {
    if (!EditorState.selectedSticker) {
        showNotification('Selecciona un sticker primero', 'error');
        return;
    }
    if (!EditorState.stream) {
        showNotification('Inicia la cámara primero', 'error');
        return;
    }

    EditorState.isRecordingGif = true;
    EditorState.gifFrames = [];

    const startBtn = document.getElementById('start-gif');
    const stopBtn = document.getElementById('stop-gif');
    if (startBtn) {
        startBtn.disabled = true;
        startBtn.style.display = 'none';
    }
    if (stopBtn) {
        stopBtn.disabled = false;
        stopBtn.style.display = 'inline-block';
    }

    showNotification('Grabando GIF... (máx. 3 s)', 'info');

    let frameCount = 0;
    // Menos frames y JPEG para no superar post_max_size (8M por defecto)
    const maxFrames = 12;

    EditorState.gifInterval = setInterval(function () {
        if (frameCount >= maxFrames) {
            stopGifRecording();
            return;
        }
        // Frame SIN sticker — el servidor aplica el sticker
        const frameData = captureBaseFrameDataUrl(true);
        if (frameData) {
            EditorState.gifFrames.push(frameData);
            frameCount++;
        }
    }, 150);
}

async function stopGifRecording() {
    EditorState.isRecordingGif = false;
    if (EditorState.gifInterval) {
        clearInterval(EditorState.gifInterval);
        EditorState.gifInterval = null;
    }

    const startBtn = document.getElementById('start-gif');
    const stopBtn = document.getElementById('stop-gif');
    if (startBtn) {
        startBtn.style.display = 'inline-block';
    }
    if (stopBtn) {
        stopBtn.disabled = true;
        stopBtn.style.display = 'none';
    }
    updateActionButtonsState();

    if (EditorState.gifFrames.length < 2) {
        showNotification('Se necesitan más frames para el GIF', 'error');
        EditorState.gifFrames = [];
        return;
    }

    showNotification('Procesando GIF en el servidor...', 'info');

    try {
        // FormData → PHP rellena $_POST (CSRF y frames funcionan)
        const formData = new FormData();
        formData.append('frames', JSON.stringify(EditorState.gifFrames));
        formData.append('sticker', EditorState.selectedSticker);
        formData.append('sticker_x', String(EditorState.stickerPosition.x));
        formData.append('sticker_y', String(EditorState.stickerPosition.y));
        formData.append('sticker_size', String(EditorState.stickerSize));
        formData.append('csrf_token', getCsrfToken());

        const response = await fetch('/editor/create-gif', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            showNotification('¡GIF creado!', 'success');
            if (result.url) {
                addImageToGallery(result.filename, result.url);
            }
        } else {
            showNotification(result.message || 'Error al crear GIF', 'error');
        }
    } catch (err) {
        console.error(err);
        showNotification('Error al crear GIF', 'error');
    } finally {
        EditorState.gifFrames = [];
    }
}

/**
 * Borrado de fotos propias
 */
function initializeDeleteButtons() {
    document.querySelectorAll('.btn-delete').forEach(function (button) {
        // Evitar duplicar listeners
        if (button.dataset.bound === '1') {
            return;
        }
        button.dataset.bound = '1';

        button.addEventListener('click', async function (e) {
            // Si está dentro de un form clásico POST, dejamos el submit nativo
            if (this.closest('form') && this.getAttribute('type') === 'submit') {
                return;
            }
            e.preventDefault();

            if (!confirm('¿Eliminar esta foto?')) {
                return;
            }

            const imageId = this.dataset.imageId;
            if (!imageId || imageId === 'new') {
                const item = this.closest('.my-image-item, .thumbnail-item');
                if (item) {
                    item.remove();
                }
                return;
            }

            try {
                const body = new URLSearchParams();
                body.set('image_id', imageId);
                body.set('csrf_token', getCsrfToken());

                const response = await fetch('/editor/delete', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: body.toString()
                });
                const result = await response.json();
                if (result.success) {
                    const item = this.closest('.my-image-item, .thumbnail-item');
                    if (item) {
                        item.remove();
                    }
                    showNotification('Imagen eliminada', 'success');
                } else {
                    showNotification(result.message || 'Error al eliminar', 'error');
                }
            } catch (err) {
                console.error(err);
                showNotification('Error al eliminar', 'error');
            }
        });
    });
}

function addImageToGallery(filename, url) {
    const gallery = document.querySelector('.my-images-grid') || document.querySelector('.thumbnails-grid');
    if (!gallery) {
        return;
    }

    const item = document.createElement('div');
    item.className = 'my-image-item';
    item.innerHTML =
        '<img src="' + url + '" alt="Foto capturada">' +
        '<div class="image-info"><span class="date">Ahora</span></div>';
    gallery.prepend(item);
}

function showNotification(message, type) {
    type = type || 'info';
    const notification = document.createElement('div');
    notification.className = 'notification notification-' + type;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(function () {
        notification.classList.add('show');
    }, 10);
    setTimeout(function () {
        notification.classList.remove('show');
        setTimeout(function () {
            notification.remove();
        }, 300);
    }, 3000);
}

window.addEventListener('beforeunload', function () {
    if (EditorState.stream) {
        EditorState.stream.getTracks().forEach(function (track) {
            track.stop();
        });
    }
});
