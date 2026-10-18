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
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
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
        // 3. Superponemos la pegatina sobre la foto (server-side)
        // ------------------------------------------------------------
        $finalImage = $this->mergeImages($baseImage, $sticker);

        // Generamos un nombre único para el archivo
        $filename = $this->generateFilename();
        $filepath = $this->uploadsPath($filename);

        // ------------------------------------------------------------
        // 4. Guardamos el resultado en disco y en la base de datos
        // ------------------------------------------------------------
        if (imagepng($finalImage, $filepath)) {
            $imageModel = $this->model('Image');
            $imageId = $imageModel->create($_SESSION['user_id'], $filename);

            // Liberamos memoria
            imagedestroy($baseImage);
            imagedestroy($sticker);
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
            imagedestroy($sticker);
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

            $finalImage = $this->mergeImages($baseImage, $sticker);
            imagedestroy($baseImage);
            imagedestroy($sticker);
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
        $this->requireAuth();

        if (!$this->isPost() || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $framesData  = json_decode($_POST['frames'] ?? '[]', true);
        $stickerPath = $_POST['sticker'] ?? '';
        $stickerX    = intval($_POST['sticker_x'] ?? 0);
        $stickerY    = intval($_POST['sticker_y'] ?? 0);
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

        $sticker = $this->loadImageWithAlpha($stickerFullPath);
        if ($sticker === false) {
            $this->json(['success' => false, 'message' => 'Failed to load sticker'], 500);
        }

        // ---------------------------------------------------------------
        // Redimensionamos la pegatina según el tamaño que eligió el usuario
        // ---------------------------------------------------------------
        $originalWidth  = imagesx($sticker);
        $originalHeight = imagesy($sticker);
        $newWidth  = max(1, intval($originalWidth  * ($stickerSize / 100)));
        $newHeight = max(1, intval($originalHeight * ($stickerSize / 100)));

        $resizedSticker = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resizedSticker, false);
        imagesavealpha($resizedSticker, true);
        $transparent = imagecolorallocatealpha($resizedSticker, 0, 0, 0, 127);
        imagefill($resizedSticker, 0, 0, $transparent);

        imagecopyresampled(
            $resizedSticker, $sticker,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $originalWidth, $originalHeight
        );
        imagedestroy($sticker);
        $sticker = $resizedSticker;

        // Aseguramos que existe la carpeta temporal
        $tempDir = $this->tempPath();
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $processedFrames = [];
        $tempFiles = [];

        // ---------------------------------------------------------------
        // Procesamos cada fotograma: decodificamos + aplicamos pegatina
        // ---------------------------------------------------------------
        foreach ($framesData as $index => $frameData) {
            $frameData = str_replace('data:image/png;base64,', '', $frameData);
            $frameData = str_replace(' ', '+', $frameData);
            $decodedFrame = base64_decode($frameData);
            if ($decodedFrame === false) {
                continue;
            }

            $frameImage = imagecreatefromstring($decodedFrame);
            if ($frameImage === false) {
                continue;
            }

            // Creamos una nueva imagen del mismo tamaño
            $finalFrame = imagecreatetruecolor(imagesx($frameImage), imagesy($frameImage));
            imagealphablending($finalFrame, false);
            imagesavealpha($finalFrame, true);

            // Copiamos el fotograma original
            imagecopy($finalFrame, $frameImage, 0, 0, 0, 0, imagesx($frameImage), imagesy($frameImage));

            // Superponemos la pegatina en la posición que el usuario eligió
            imagealphablending($finalFrame, true);
            imagecopy(
                $finalFrame, $sticker,
                $stickerX, $stickerY,
                0, 0,
                imagesx($sticker), imagesy($sticker)
            );

            // Guardamos el fotograma procesado en un archivo temporal
            $tempFilename = $tempDir . '/frame_' . time() . '_' . $index . '_' . bin2hex(random_bytes(4)) . '.png';
            imagepng($finalFrame, $tempFilename);

            $processedFrames[] = $tempFilename;
            $tempFiles[] = $tempFilename;

            imagedestroy($frameImage);
            imagedestroy($finalFrame);
        }

        imagedestroy($sticker);

        if (count($processedFrames) < 2) {
            $this->cleanupTemp($tempFiles);
            $this->json(['success' => false, 'message' => 'Failed to process frames'], 500);
        }

        // ---------------------------------------------------------------
        // Creamos el GIF final
        // ---------------------------------------------------------------
        $gifFilename = 'gif_' . $_SESSION['user_id'] . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.gif';
        $gifPath = $this->uploadsPath($gifFilename);
        $success = false;
        $error = '';

        // Método preferido: extensión Imagick (la más limpia)
        if (extension_loaded('imagick')) {
            try {
                $animation = new Imagick();
                $animation->setFormat('gif');

                foreach ($processedFrames as $frame) {
                    $image = new Imagick($frame);
                    $image->setImageDelay(10); // 10 = 100 milisegundos
                    $animation->addImage($image);
                    $image->clear();
                }

                $animation->setImageIterations(0); // 0 = bucle infinito
                $animation->writeImages($gifPath, true);
                $animation->clear();
                $success = true;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        // Método de respaldo: comando "convert" de ImageMagick
        elseif (shell_exec('which convert') !== null) {
            $framesList = implode(' ', array_map('escapeshellarg', $processedFrames));
            $command = "convert -delay 10 -loop 0 $framesList " . escapeshellarg($gifPath);
            exec($command, $output, $returnVar);
            $success = ($returnVar === 0 && file_exists($gifPath));
            if (!$success) {
                $error = 'convert command failed';
            }
        } else {
            $error = 'ImageMagick (imagick extension or convert binary) is not available';
        }

        // Limpiamos los archivos temporales sí o sí
        $this->cleanupTemp($tempFiles);

        if ($success && file_exists($gifPath)) {
            $imageModel = $this->model('Image');
            $imageId = $imageModel->create($_SESSION['user_id'], $gifFilename);

            if ($imageId) {
                $this->json([
                    'success'  => true,
                    'image_id' => $imageId,
                    'filename' => $gifFilename,
                    'url'      => $this->url('uploads/images/' . $gifFilename)
                ]);
            } else {
                @unlink($gifPath);
                $this->json(['success' => false, 'message' => 'Failed to save GIF to database'], 500);
            }
        } else {
            $this->json([
                'success' => false,
                'message' => 'Failed to create GIF: ' . ($error ?: 'Unknown error')
            ], 500);
        }
    }

    // =========================================================================
    // MÉTODOS PRIVADOS DE AYUDA
    // =========================================================================

    /**
     * Devuelve la lista de pegatinas disponibles (archivos .png de /public/stickers)
     */
    private function getStickers() {
        $stickersDir = $this->publicPath('stickers');
        $stickers = [];

        if (is_dir($stickersDir)) {
            $files = scandir($stickersDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && preg_match('/\.png$/i', $file)) {
                    $stickers[] = 'stickers/' . $file;
                }
            }
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
     * Superpone una pegatina centrada sobre una imagen base
     * (usado en capture y upload)
     */
    private function mergeImages($baseImage, $sticker) {
        $baseWidth  = imagesx($baseImage);
        $baseHeight = imagesy($baseImage);
        $stickerWidth  = imagesx($sticker);
        $stickerHeight = imagesy($sticker);

        // Creamos una nueva imagen del tamaño de la base
        $finalImage = imagecreatetruecolor($baseWidth, $baseHeight);
        imagealphablending($finalImage, false);
        imagesavealpha($finalImage, true);

        // Copiamos la foto base
        imagecopy($finalImage, $baseImage, 0, 0, 0, 0, $baseWidth, $baseHeight);

        // Calculamos la posición centrada de la pegatina
        $x = ($baseWidth  - $stickerWidth)  / 2;
        $y = ($baseHeight - $stickerHeight) / 2;

        // Superponemos la pegatina
        imagealphablending($finalImage, true);
        imagecopy($finalImage, $sticker, (int)$x, (int)$y, 0, 0, $stickerWidth, $stickerHeight);

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
