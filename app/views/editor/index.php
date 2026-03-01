<?php $title = 'Editor de Fotos'; include __DIR__ . '/../partials/header.php'; ?>

<div class="editor-container">
    <h1>Editor de Fotos 📷</h1>
    <p class="subtitle">Crea tu obra maestra con stickers</p>
    
    <div class="editor-tabs">
        <button class="tab-btn active" data-tab="webcam">📹 Webcam</button>
        <button class="tab-btn" data-tab="upload">📁 Subir Archivo</button>
    </div>
    
    <!-- Tab: Webcam -->
    <div class="tab-content active" id="tab-webcam">
        <div class="webcam-section">
            <div class="camera-container">
                <!-- Vista previa en vivo (BONUS) -->
                <canvas id="live-preview" width="640" height="480"></canvas>
                <video id="webcam" autoplay playsinline width="640" height="480"></video>
                
                <div class="camera-controls">
                    <button id="start-camera" class="btn btn-primary">🎥 Iniciar Cámara</button>
                    <button id="capture-photo" class="btn btn-success" disabled>📸 Capturar Foto</button>
                    <button id="start-gif" class="btn btn-secondary" disabled>🎬 Grabar GIF</button>
                    <button id="stop-gif" class="btn btn-danger" disabled style="display:none;">⏹️ Detener GIF</button>
                </div>
                
                <!-- Control de tamaño de sticker (BONUS) -->
                <div class="sticker-size-control" style="display:none;">
                    <label>Tamaño del Sticker:</label>
                    <input type="range" id="sticker-size" min="50" max="200" value="100">
                    <span id="size-value">100%</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab: Upload -->
    <div class="tab-content" id="tab-upload">
        <div class="upload-section">
            <form id="upload-form" method="POST" enctype="multipart/form-data">
                <div class="upload-area" id="drop-zone">
                    <input type="file" id="file-input" name="photo" accept="image/jpeg,image/png" required>
                    <label for="file-input" class="upload-label">
                        <span class="upload-icon">📁</span>
                        <p>Arrastra una imagen aquí o haz clic para seleccionar</p>
                        <small>Formatos soportados: JPG, PNG (máx. 5MB)</small>
                    </label>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            </form>
            
            <!-- Preview de la imagen subida -->
            <div id="upload-preview" style="display:none;">
                <canvas id="upload-canvas" width="640" height="480"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Stickers disponibles -->
    <div class="stickers-section">
        <h3>Stickers Disponibles</h3>
        <p class="help-text">Haz clic en un sticker y luego arrastra sobre la imagen para posicionarlo</p>
        
        <div class="stickers-grid">
            <?php foreach ($stickers as $sticker): ?>
                <div class="sticker-item" data-sticker="<?php echo htmlspecialchars($sticker); ?>">
                    <img src="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/uploads/stickers/<?php echo htmlspecialchars($sticker); ?>" 
                         alt="Sticker">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Canvas oculto para captura -->
    <canvas id="capture-canvas" width="640" height="480" style="display:none;"></canvas>
    
    <!-- Mis fotos recientes -->
    <?php if (!empty($myImages)): ?>
        <div class="my-images-section">
            <h3>Mis Fotos Recientes</h3>
            <div class="my-images-grid">
                <?php foreach ($myImages as $image): ?>
                    <div class="my-image-item">
                        <img src="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/uploads/images/<?php echo htmlspecialchars($image['filename']); ?>" 
                             alt="Mi foto">
                        <div class="image-info">
                            <span class="date"><?php echo date('d M Y', strtotime($image['created_at'])); ?></span>
                            <div class="stats">
                                <span>❤️ <?php echo $image['like_count']; ?></span>
                                <span>💬 <?php echo $image['comment_count']; ?></span>
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
    background: black;
    border-radius: 12px;
    overflow: hidden;
}

#webcam, #live-preview {
    display: block;
    max-width: 100%;
    height: auto;
}

#live-preview {
    position: absolute;
    top: 0;
    left: 0;
    display: none;
    cursor: crosshair;
}

#live-preview.active {
    display: block;
}

.camera-controls {
    padding: 1rem;
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
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
    max-width: 640px;
    margin: 0 auto;
    padding: 3rem;
    border: 3px dashed var(--light);
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
    font-size: 4rem;
    display: block;
    margin-bottom: 1rem;
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
</style>

<script src="<?php echo rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'); ?>/js/editor.js"></script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
