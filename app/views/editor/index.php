<?php
$title = function_exists('__') ? __('editor.title') : 'Editor';
include __DIR__ . '/../partials/header.php';
?>

<div class="editor-page">
<div class="editor-container">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

    <h1><?php echo function_exists('__') ? __('editor.title') : 'Editor de Fotos'; ?> 📷</h1>
    <p class="subtitle"><?php echo function_exists('__') ? __('editor.subtitle') : 'Crea tu obra maestra con stickers'; ?></p>
    
    <div class="editor-tabs">
        <button class="tab-btn active" data-tab="webcam">📹 <?php echo function_exists('__') ? __('editor.webcam') : 'Webcam'; ?></button>
        <button class="tab-btn" data-tab="upload">📁 <?php echo function_exists('__') ? __('editor.upload') : 'Subir Archivo'; ?></button>
    </div>
    
    <!-- Tab: Webcam -->
    <div class="tab-content active" id="tab-webcam">
        <div class="webcam-section">
            <!-- Solo vídeo/canvas (puede tener overflow hidden) -->
            <div class="camera-container" id="camera-media">
                <video id="webcam" autoplay playsinline width="640" height="480"></video>
                <canvas id="live-preview" width="640" height="480"></canvas>
            </div>

            <!-- Botones FUERA del contenedor con overflow, siempre visibles -->
            <div class="camera-controls">
                <button type="button" id="start-camera" class="btn btn-primary">🎥 <?php echo function_exists('__') ? __('editor.start_camera') : 'Iniciar Cámara'; ?></button>
                <button type="button" id="capture-photo" class="btn btn-success" disabled>📸 <?php echo function_exists('__') ? __('editor.capture') : 'Capturar Foto'; ?></button>
                <button type="button" id="start-gif" class="btn btn-secondary" disabled>🎬 <?php echo function_exists('__') ? __('editor.gif') : 'Grabar GIF'; ?></button>
                <button type="button" id="stop-gif" class="btn btn-danger" disabled style="display:none;">⏹️ <?php echo function_exists('__') ? __('editor.stop_gif') : 'Detener GIF'; ?></button>
            </div>

            <div class="sticker-size-control" id="sticker-size-control" style="display:none;">
                <label><?php echo function_exists('__') ? __('editor.sticker_size') : 'Tamaño del Sticker'; ?>:</label>
                <input type="range" id="sticker-size" min="50" max="200" value="100">
                <span id="size-value">100px</span>
            </div>
        </div>
    </div>
    
    <!-- Tab: Upload -->
    <div class="tab-content" id="tab-upload">
        <div class="upload-section">
            <form id="upload-form" method="POST" enctype="multipart/form-data">
                <div class="upload-area" id="drop-zone">
                    <!-- Input a pantalla completa de la zona: clic fiable en todos los navegadores -->
                    <input type="file" id="file-input" name="image"
                           accept="image/jpeg,image/png,image/jpg,.jpg,.jpeg,.png"
                           required
                           class="upload-file-input">
                    <div class="upload-label" aria-hidden="true">
                        <span class="upload-icon">📁</span>
                        <p>Arrastra una imagen aquí o haz clic para seleccionar</p>
                        <small>Formatos soportados: JPG, PNG (máx. 5MB)</small>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <button type="button" id="upload-submit" class="btn btn-success" style="margin-top:1rem;" disabled>
                    Subir imagen
                </button>
            </form>
            
            <!-- Preview de la imagen subida -->
            <div id="upload-preview" style="display:none;">
                <canvas id="upload-canvas" width="640" height="480"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Stickers disponibles -->
    <div class="stickers-section">
        <h3><?php echo function_exists('__') ? __('editor.stickers') : 'Stickers Disponibles'; ?></h3>
        <p class="help-text"><?php echo function_exists('__') ? __('editor.stickers_help') : 'Haz clic en un sticker...'; ?></p>
        
        <div class="stickers-grid">
            <?php foreach ($stickers as $sticker): ?>
                <div class="sticker-item" data-sticker="<?php echo htmlspecialchars($sticker); ?>">
                    <img src="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/<?php echo htmlspecialchars($sticker); ?>" 
                         alt="Sticker">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Canvas oculto para captura -->
    <canvas id="capture-canvas" width="640" height="480" style="display:none;"></canvas>
    
    <!-- Mis fotos recientes -->
    <?php if (!empty($userImages)): ?>
        <div class="my-images-section">
            <h3>Mis Fotos Recientes</h3>
            <div class="my-images-grid">
                <?php foreach ($userImages as $image): ?>
                    <div class="my-image-item">
                        <img src="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/uploads/images/<?php echo htmlspecialchars($image['filename']); ?>" 
                             alt="Mi foto">
                        <div class="image-info">
                            <span class="date"><?php echo date('d M Y', strtotime($image['created_at'])); ?></span>
                            <div class="stats">
                                <span>❤️ <?php echo isset($image['like_count']) ? $image['like_count'] : 0; ?></span>
                                <span>💬 <?php echo isset($image['comment_count']) ? $image['comment_count'] : 0; ?></span>
                            </div>
                        </div>
                        <form method="POST" action="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/editor/delete" onsubmit="return confirm('¿Estás seguro de eliminar esta foto?');">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                            <button type="submit" class="btn-delete">🗑️</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.editor-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.subtitle {
    text-align: center;
    color: var(--gray);
    margin-bottom: 2rem;
}

.editor-tabs {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.tab-btn {
    padding: 1rem 2rem;
    border: 2px solid var(--light);
    background: white;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
}

.tab-btn:hover {
    border-color: var(--primary-color);
}

.tab-btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.webcam-section {
    text-align: center;
}

.camera-container {
    position: relative;
    display: inline-block;
    background: #111;
    border-radius: 12px;
    overflow: hidden;
    max-width: 100%;
    line-height: 0; /* evita hueco bajo el vídeo */
}

#webcam, #live-preview {
    display: block;
    max-width: 100%;
    height: auto;
    vertical-align: top;
}

#live-preview {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: none;
    cursor: crosshair;
}

#live-preview.active {
    display: block;
    z-index: 2;
    pointer-events: auto;
}

#webcam {
    pointer-events: none;
}

.camera-controls {
    margin: 1.25rem auto 0;
    padding: 1rem 1.25rem;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    max-width: 640px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.camera-controls .btn {
    min-width: 140px;
    font-weight: 600;
}

.sticker-size-control {
    padding: 1rem;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    text-align: center;
}

.sticker-size-control input[type="range"] {
    width: 200px;
    margin: 0 1rem;
}

#size-value {
    display: inline-block;
    min-width: 50px;
}

.upload-section {
    text-align: center;
}

.upload-area {
    max-width: 480px;
    margin: 0 auto;
    padding: 1.25rem 1.5rem;
    min-height: 120px;
    border: 2px dashed var(--light);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-area:hover {
    border-color: var(--primary-color);
    background: var(--light);
}

.upload-area.drag-over {
    border-color: var(--success-color);
    background: #e6f7e6;
}

#file-input {
    display: none;
}

.upload-label {
    cursor: pointer;
    display: block;
}

.upload-icon {
    font-size: 2rem;
    display: block;
    margin-bottom: 0.35rem;
}

#upload-preview, #upload-canvas {
    max-width: 100%;
    height: auto;
    margin: 2rem auto;
}

.stickers-section {
    margin-top: 3rem;
    padding: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.help-text {
    text-align: center;
    color: var(--gray);
    margin-bottom: 1.5rem;
}

.stickers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
}

.sticker-item {
    padding: 1rem;
    background: var(--light);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 3px solid transparent;
}

.sticker-item:hover {
    transform: scale(1.1);
    border-color: var(--primary-color);
}

.sticker-item.selected {
    border-color: var(--success-color);
    background: #e6f7e6;
}

.sticker-item img {
    width: 100%;
    height: 100px;
    object-fit: contain;
}

.my-images-section {
    margin-top: 3rem;
    padding: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.my-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.my-image-item {
    position: relative;
    background: var(--light);
    border-radius: 8px;
    overflow: hidden;
}

.my-image-item img {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.image-info {
    padding: 1rem;
}

.stats {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
}

.btn-delete {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--danger-color);
    color: white;
    border: none;
    padding: 0.5rem;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.2rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-delete:hover {
    background: #c41e1e;
}

/* GIF recording indicator */
.recording-indicator {
    position: absolute;
    top: 10px;
    left: 10px;
    background: var(--danger-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: bold;
    animation: blink 1s infinite;
}

@keyframes blink {
    0%, 50%, 100% { opacity: 1; }
    25%, 75% { opacity: 0.5; }
}

@media (max-width: 768px) {
    .stickers-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    }
    
    .my-images-grid {
        grid-template-columns: 1fr;
    }
    
    .camera-controls {
        flex-direction: column;
    }
    
    .camera-controls .btn {
        width: 100%;
    }
}

/* Input invisible encima de la zona de drop: clic y teclado OK */
.upload-area {
    position: relative;
}
.upload-file-input {
    position: absolute !important;
    inset: 0 !important;
    width: 100% !important;
    height: 100% !important;
    opacity: 0 !important;
    cursor: pointer !important;
    display: block !important;
    z-index: 5 !important;
    font-size: 0;
}
.upload-label {
    pointer-events: none;
}

</style>

<script src="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/js/editor.js"></script>

</div><!-- .editor-page -->
</div><!-- .editor-page -->
<?php include __DIR__ . '/../partials/footer.php'; ?>
