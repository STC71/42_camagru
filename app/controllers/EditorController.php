<?php

/**
 * =============================================================================
 * EditorController.php
 * =============================================================================
 * Este archivo es el "cerebro" de la parte de edición de fotos.
 *
 * Analogía cotidiana:
 * Imagina que entras en un estudio de fotografía. Hay una persona que te recibe,
 * te enseña las pegatinas disponibles, te deja hacer la foto, la edita y te
 * entrega el resultado. Esa persona es este controlador.
 *
 * Responsabilidades principales:
 * 1. Mostrar la página del editor (webcam + pegatinas)
 * 2. Capturar foto desde la webcam + pegatina
 * 3. Permitir subir una foto desde el ordenador + pegatina (opcional)
 * 4. Borrar fotos propias
 * 5. Crear GIFs animados (bonus)
 * =============================================================================
 */
class EditorController extends Controller {

    /**
     * -------------------------------------------------------------------------
     * Muestra la página principal del editor
     * -------------------------------------------------------------------------
     * Solo pueden entrar usuarios logueados.
     * Carga las últimas 10 fotos del usuario y la lista de pegatinas.
     */
    public function index() {
        // Si el usuario no está logueado, le mandamos al login
        $this->requireAuth();

        // Pedimos al modelo Image las últimas 10 fotos del usuario actual
        $imageModel = $this->model('Image');
        $userImages = $imageModel->findByUserId($_SESSION['user_id'], 10);

        // Obtenemos la lista de pegatinas disponibles
        $stickers = $this->getStickers();

        // Enviamos todos los datos a la vista (el HTML)
        $this->view('editor/index', [
            'userImages' => $userImages,
            'stickers'   => $stickers,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    /**
     * -------------------------------------------------------------------------
     * Captura una foto desde la webcam + pegatina
     * -------------------------------------------------------------------------
     * El navegador nos envía la imagen en formato base64 (texto).
     * Nosotros la convertimos en imagen real, le ponemos la pegatina
     * y la guardamos en el servidor.
     */
    public function capture() {
        $this->requireAuth();

        // Solo aceptamos peticiones POST y con token CSRF válido
        if (!$this->isPost() || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $imageData   = $_POST['image']   ?? '';
        $stickerPath = $_POST['sticker'] ?? '';
        // Posición del sticker en % del canvas (centro) y tamaño en px — enviados por el cliente
        $stickerX    = floatval($_POST['sticker_x'] ?? 50);
        $stickerY    = floatval($_POST['sticker_y'] ?? 50);
        $stickerSize = intval($_POST['sticker_size'] ?? 100);

        // Validaciones básicas
        if (empty($imageData)) {
            $this->json(['success' => false, 'message' => 'No image data provided'], 400);
        }
        if (empty($stickerPath)) {
            $this->json(['success' => false, 'message' => 'No sticker selected'], 400);
        }

        // ------------------------------------------------------------
        // 1. Convertimos el texto base64 en una imagen real de PHP
        // ------------------------------------------------------------
        // El navegador nos manda algo como:
        // "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA..."
        // Nosotros quitamos la parte del principio y decodificamos.
        // Aceptamos PNG o JPEG en base64
        if (preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $imageData)) {
            $imageData = preg_replace('/^data:image\/(png|jpeg|jpg);base64,/', '', $imageData);
        }
        $imageData = str_replace(' ', '+', $imageData);
        $decodedImage = base64_decode($imageData);

        if ($decodedImage === false) {
            $this->json(['success' => false, 'message' => 'Invalid image data'], 400);
        }

        // Creamos un recurso de imagen de GD a partir de los bytes
        $baseImage = imagecreatefromstring($decodedImage);
        if ($baseImage === false) {
            $this->json(['success' => false, 'message' => 'Failed to process image'], 500);
        }

        // ------------------------------------------------------------
        // 2. Cargamos la pegatina (tiene canal alfa = transparencia)
        // ------------------------------------------------------------
        $stickerFullPath = $this->publicPath($stickerPath);
        if (!file_exists($stickerFullPath)) {
            imagedestroy($baseImage);
            $this->json(['success' => false, 'message' => 'Sticker not found'], 404);
        }

        $sticker = $this->loadImageWithAlpha($stickerFullPath);
        if ($sticker === false) {
            imagedestroy($baseImage);
            $this->json(['success' => false, 'message' => 'Failed to load sticker'], 500);
        }

        // ------------------------------------------------------------
        // 3. Superponemos la pegatina en el SERVIDOR (posición del usuario)
        //    La imagen recibida es la BASE sin sticker (sin duplicar).
        // ------------------------------------------------------------
        $finalImage = $this->mergeImagesAt($baseImage, $sticker, $stickerX, $stickerY, $stickerSize);

        // Generamos un nombre único para el archivo
        $filename = $this->generateFilename();
        $filepath = $this->uploadsPath($filename);

        // Asegurar carpeta de subida escribible (volumen Docker / sgoinfre)
        $uploadDir = dirname($filepath);
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }
        if (!is_writable($uploadDir)) {
            @chmod($uploadDir, 0777);
        }
        if (!is_writable($uploadDir)) {
            imagedestroy($baseImage);
            imagedestroy($finalImage);
            $this->json([
                'success' => false,
                'message' => 'Upload directory not writable: ' . $uploadDir
            ], 500);
        }

        // ------------------------------------------------------------
        // 4. Guardamos el resultado en disco y en la base de datos
        // ------------------------------------------------------------
        if (@imagepng($finalImage, $filepath)) {
            $imageModel = $this->model('Image');
            $imageId = $imageModel->create($_SESSION['user_id'], $filename);

            // Liberamos memoria (sticker ya se libera en mergeImagesAt)
            imagedestroy($baseImage);
            imagedestroy($finalImage);

            if ($imageId) {
                $this->json([
                    'success'  => true,
                    'image_id' => $imageId,
                    'filename' => $filename,
                    'url'      => $this->url('uploads/images/' . $filename)
                ]);
            } else {
                @unlink($filepath); // Si falla la BD, borramos el archivo
                $this->json(['success' => false, 'message' => 'Failed to save image'], 500);
            }
        } else {
            imagedestroy($baseImage);
            imagedestroy($finalImage);
            $this->json(['success' => false, 'message' => 'Failed to save image'], 500);
        }
    }

    /**
     * -------------------------------------------------------------------------
     * Sube una foto desde el ordenador del usuario
     * -------------------------------------------------------------------------
     * Permite 0 o 1 pegatina (requisito de la evaluación).
     * Si no se envía pegatina, se guarda la imagen tal cual.
     */
    public function upload() {
        $this->requireAuth();

        if (!$this->isPost() || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'No file uploaded'], 400);
        }

        // La pegatina es opcional en la subida de archivo
        $stickerPath  = $_POST['sticker'] ?? '';
        $uploadedFile = $_FILES['image']['tmp_name'];

        // Validamos que sea realmente una imagen y que no sea demasiado grande
        if (!Validator::isImage($uploadedFile)) {
            $this->json(['success' => false, 'message' => 'Invalid image format'], 400);
        }
        if (!Validator::maxFileSize($uploadedFile)) {
            $this->json(['success' => false, 'message' => 'File too large (max 5MB)'], 400);
        }

        $baseImage = $this->loadImageWithAlpha($uploadedFile);
        if ($baseImage === false) {
            $this->json(['success' => false, 'message' => 'Failed to process image'], 500);
        }

        // Por defecto la imagen final es la que subió el usuario
        $finalImage = $baseImage;

        // Si el usuario eligió una pegatina, la superponemos
        if (!empty($stickerPath)) {
            $stickerFullPath = $this->publicPath($stickerPath);
            if (!file_exists($stickerFullPath)) {
                imagedestroy($baseImage);
                $this->json(['success' => false, 'message' => 'Sticker not found'], 404);
            }

            $sticker = $this->loadImageWithAlpha($stickerFullPath);
            if ($sticker === false) {
                imagedestroy($baseImage);
                $this->json(['success' => false, 'message' => 'Failed to load sticker'], 500);
            }

            $ux = floatval($_POST['sticker_x'] ?? 50);
            $uy = floatval($_POST['sticker_y'] ?? 50);
            $us = intval($_POST['sticker_size'] ?? 100);
            $finalImage = $this->mergeImagesAt($baseImage, $sticker, $ux, $uy, $us);
            imagedestroy($baseImage);
            // sticker ya destruido dentro de mergeImagesAt
        }

        $filename = $this->generateFilename();
        $filepath = $this->uploadsPath($filename);

        if (imagepng($finalImage, $filepath)) {
            $imageModel = $this->model('Image');
            $imageId = $imageModel->create($_SESSION['user_id'], $filename);

            if (isset($finalImage)) {
                imagedestroy($finalImage);
            }

            if ($imageId) {
                $this->json([
                    'success'  => true,
                    'image_id' => $imageId,
                    'filename' => $filename,
                    'url'      => $this->url('uploads/images/' . $filename)
                ]);
            } else {
                @unlink($filepath);
                $this->json(['success' => false, 'message' => 'Failed to save image'], 500);
            }
        } else {
            if (isset($finalImage)) {
                imagedestroy($finalImage);
            }
            $this->json(['success' => false, 'message' => 'Failed to save image'], 500);
        }
    }

    /**
     * -------------------------------------------------------------------------
     * Elimina una foto del usuario
     * -------------------------------------------------------------------------
     * Solo se puede borrar una foto propia (el modelo comprueba el ownership).
     */
    public function delete() {
        $this->requireAuth();

        if (!$this->isPost() || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $imageId = intval($_POST['image_id'] ?? 0);
        $imageModel = $this->model('Image');

        if ($imageModel->delete($imageId, $_SESSION['user_id'])) {
            $this->json(['success' => true]);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete image'], 500);
        }
    }

    /**
     * -------------------------------------------------------------------------
     * BONUS: Genera un GIF animado
     * -------------------------------------------------------------------------
     * El navegador nos envía varios fotogramas (frames) en base64.
     * Nosotros aplicamos la pegatina a cada uno y los unimos en un GIF.
     *
     * Analogía:
     * Es como hacer un flipbook (librito de dibujos que al pasar las páginas
     * parece que se mueve). Cada frame es una página del librito.
     */
    public function createGif() {
        /*
         * GIF animado (bonus):
         * - Decodifica cada frame y aplica el sticker con Imagick (JPEG/PNG sin depender de GD).
         * - Si Imagick no está, intenta el binario "convert".
         * Así evitamos el error "No JPEG support in this PHP build" de imagecreatefromstring().
         */
        $this->requireAuth();

        if (!$this->isPost() || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        if (!extension_loaded('imagick') && shell_exec('which convert') === null) {
            $this->json([
                'success' => false,
                'message' => 'ImageMagick is not available (need imagick extension or convert binary)'
            ], 500);
        }

        $framesData  = json_decode($_POST['frames'] ?? '[]', true);
        $stickerPath = $_POST['sticker'] ?? '';
        $stickerX    = floatval($_POST['sticker_x'] ?? 50);
        $stickerY    = floatval($_POST['sticker_y'] ?? 50);
        $stickerSize = intval($_POST['sticker_size'] ?? 100);

        if (empty($framesData) || !is_array($framesData) || count($framesData) < 2) {
            $this->json(['success' => false, 'message' => 'Need at least 2 frames'], 400);
        }
        if (empty($stickerPath)) {
            $this->json(['success' => false, 'message' => 'No sticker selected'], 400);
        }

        $stickerFullPath = $this->publicPath($stickerPath);
        if (!file_exists($stickerFullPath)) {
            $this->json(['success' => false, 'message' => 'Sticker not found'], 404);
        }

        $tempDir = $this->tempPath();
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $processedFrames = [];
        $tempFiles = [];
        $error = '';

        try {
            if (!extension_loaded('imagick')) {
                throw new Exception('imagick extension missing');
            }

            $stickerBlob = file_get_contents($stickerFullPath);
            if ($stickerBlob === false) {
                throw new Exception('Cannot read sticker file');
            }

            foreach ($framesData as $index => $frameData) {
                if (!is_string($frameData) || $frameData === '') {
                    continue;
                }

                // data:image/png;base64,... o data:image/jpeg;base64,...
                if (preg_match('/^data:image\/[a-zA-Z0-9+.-]+;base64,/', $frameData)) {
                    $frameData = preg_replace('/^data:image\/[a-zA-Z0-9+.-]+;base64,/', '', $frameData);
                }
                $frameData = str_replace(' ', '+', $frameData);
                $decoded = base64_decode($frameData, true);
                if ($decoded === false || $decoded === '') {
                    continue;
                }

                $frame = new Imagick();
                $frame->readImageBlob($decoded);

                $fw = $frame->getImageWidth();
                $fh = $frame->getImageHeight();
                if ($fw < 1 || $fh < 1) {
                    $frame->clear();
                    continue;
                }

                // Sticker fresco por frame (Imagick muta al redimensionar)
                $sticker = new Imagick();
                $sticker->readImageBlob($stickerBlob);
                $sticker->setImageBackgroundColor(new ImagickPixel('transparent'));
                $sw = max(10, $stickerSize);
                $sticker->thumbnailImage($sw, 0);

                $sw = $sticker->getImageWidth();
                $sh = $sticker->getImageHeight();
                $px = (int)(($stickerX / 100.0) * $fw - $sw / 2);
                $py = (int)(($stickerY / 100.0) * $fh - $sh / 2);

                $frame->compositeImage($sticker, Imagick::COMPOSITE_OVER, $px, $py);
                $sticker->clear();

                $tempFilename = $tempDir . '/frame_' . time() . '_' . $index . '_' . bin2hex(random_bytes(4)) . '.png';
                $frame->setImageFormat('png');
                $frame->writeImage($tempFilename);
                $frame->clear();

                $processedFrames[] = $tempFilename;
                $tempFiles[] = $tempFilename;
            }

            if (count($processedFrames) < 2) {
                $this->cleanupTemp($tempFiles);
                $this->json(['success' => false, 'message' => 'Failed to process frames (need >= 2 valid frames)'], 400);
            }

            $gifFilename = 'gif_' . $_SESSION['user_id'] . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.gif';
            $gifPath = $this->uploadsPath($gifFilename);

            $animation = new Imagick();
            $animation->setFormat('gif');
            foreach ($processedFrames as $path) {
                $img = new Imagick($path);
                $img->setImageDelay(20); // 20 = 200 ms por frame → ~2 s con 10 frames
                $animation->addImage($img);
                $img->clear();
            }
            $animation->setImageIterations(0);
            $animation->writeImages($gifPath, true);
            $animation->clear();

            $this->cleanupTemp($tempFiles);

            if (!file_exists($gifPath)) {
                $this->json(['success' => false, 'message' => 'GIF file was not written'], 500);
            }

            $imageModel = $this->model('Image');
            $imageId = $imageModel->create($_SESSION['user_id'], $gifFilename);
            if ($imageId) {
                $this->json([
                    'success'  => true,
                    'image_id' => $imageId,
                    'filename' => $gifFilename,
                    'url'      => $this->url('uploads/images/' . $gifFilename)
                ]);
            }

            @unlink($gifPath);
            $this->json(['success' => false, 'message' => 'Failed to save GIF to database'], 500);
        } catch (Throwable $e) {
            if (!empty($tempFiles)) {
                $this->cleanupTemp($tempFiles);
            }
            $this->json([
                'success' => false,
                'message' => 'Failed to create GIF: ' . $e->getMessage()
            ], 500);
        }
    }


    private function getStickers() {
        $stickersDir = $this->publicPath('stickers');
        $stickers = [];

        if (is_dir($stickersDir)) {
            $files = scandir($stickersDir);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, ['png', 'gif', 'jpg', 'jpeg', 'webp'], true)) {
                    $stickers[] = 'stickers/' . $file;
                }
            }
            // Orden estable: 00_42 primero, luego el resto alfabético
            sort($stickers, SORT_STRING);
        }

        return $stickers;
    }

    /**
     * Carga una imagen respetando la transparencia (canal alfa)
     * Soporta JPEG, PNG y GIF.
     */
    private function loadImageWithAlpha($filepath) {
        $info = getimagesize($filepath);
        if ($info === false) {
            return false;
        }

        $image = null;
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($filepath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($filepath);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($filepath);
                break;
            default:
                return false;
        }

        if ($image === false) {
            return false;
        }

        // Muy importante: decimos a GD que respete la transparencia
        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    /**
     * Superpone una pegatina centrada (fallback para upload sin posición)
     */
    private function mergeImages($baseImage, $sticker) {
        return $this->mergeImagesAt($baseImage, $sticker, 50, 50, imagesx($sticker));
    }

    /**
     * Redimensiona el sticker a un ancho concreto manteniendo proporción y alpha
     */
    private function resizeStickerToWidth($sticker, $targetWidth) {
        $originalWidth  = imagesx($sticker);
        $originalHeight = imagesy($sticker);
        $targetWidth = max(1, (int)$targetWidth);
        $targetHeight = max(1, (int)round($originalHeight * ($targetWidth / $originalWidth)));

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefill($resized, 0, 0, $transparent);

        imagecopyresampled(
            $resized, $sticker,
            0, 0, 0, 0,
            $targetWidth, $targetHeight,
            $originalWidth, $originalHeight
        );
        imagedestroy($sticker);
        return $resized;
    }

    /**
     * Superpone una pegatina en una posición (% del canvas, centro del sticker)
     * y con un tamaño en píxeles. Toda la composición es server-side (GD).
     *
     * @param resource $baseImage
     * @param resource $sticker
     * @param float $posXPercent 0-100
     * @param float $posYPercent 0-100
     * @param int $stickerSizePx ancho deseado del sticker
     */
    private function mergeImagesAt($baseImage, $sticker, $posXPercent, $posYPercent, $stickerSizePx) {
        $baseWidth  = imagesx($baseImage);
        $baseHeight = imagesy($baseImage);

        $sticker = $this->resizeStickerToWidth($sticker, max(10, (int)$stickerSizePx));
        $stickerWidth  = imagesx($sticker);
        $stickerHeight = imagesy($sticker);

        $finalImage = imagecreatetruecolor($baseWidth, $baseHeight);
        imagealphablending($finalImage, false);
        imagesavealpha($finalImage, true);

        imagecopy($finalImage, $baseImage, 0, 0, 0, 0, $baseWidth, $baseHeight);

        $x = (int)(($posXPercent / 100.0) * $baseWidth  - $stickerWidth / 2);
        $y = (int)(($posYPercent / 100.0) * $baseHeight - $stickerHeight / 2);

        imagealphablending($finalImage, true);
        imagecopy($finalImage, $sticker, $x, $y, 0, 0, $stickerWidth, $stickerHeight);

        imagedestroy($sticker);
        return $finalImage;
    }

    /**
     * Genera un nombre de archivo único y seguro
     */
    private function generateFilename() {
        return 'img_' . $_SESSION['user_id'] . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.png';
    }

    /**
     * -------------------------------------------------------------------------
     * Helpers de rutas (absolutamente robustos)
     * -------------------------------------------------------------------------
     * Usamos __DIR__ para no depender nunca del "directorio de trabajo" actual.
     * Así evitamos errores cuando el código se ejecuta desde sitios diferentes.
     */

    /** Ruta absoluta a la carpeta public/ */
    private function publicPath($relative = '') {
        $base = realpath(__DIR__ . '/../../public');
        return $base . ($relative ? '/' . ltrim($relative, '/') : '');
    }

    /** Ruta absoluta a un archivo dentro de uploads/images/ */
    private function uploadsPath($filename) {
        return $this->publicPath('uploads/images/' . $filename);
    }

    /** Ruta absoluta a la carpeta temporal */
    private function tempPath() {
        return $this->publicPath('uploads/temp');
    }

    /** Borra una lista de archivos temporales */
    private function cleanupTemp(array $files) {
        foreach ($files as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }
}
