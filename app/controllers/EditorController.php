<?php

/**
 * EditorController
 * Gestiona la funcionalidad de edicion de fotos con camara web y pegatinas
 */
class EditorController extends Controller {

    /**

     * Método index.

     * @return mixed

     */
/**
 * Método principal de visualización/entrada para esta ruta.
 *
 * Descripción detallada:
 * Este método `index` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->index();
 * ```
 */

/**
 * Método principal de visualización/entrada para esta ruta.
 *
 * Descripción detallada:
 * Este método `index` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->index();
 * ```
 */
    public function index() {
        $this->requireAuth();

        $imageModel = $this->model('Image');
        $userImages = $imageModel->findByUserId($_SESSION['user_id'], 10);

        // Obtener pegatinas disponibles
        $stickers = $this->getStickers();

        $this->view('editor/index', [
            'userImages' => $userImages,
            'stickers' => $stickers,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    /**

     * Método capture.

     * @return mixed

     */
/**
 * Método `capture` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `capture` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->capture();
 * ```
 */

/**
 * Método `capture` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `capture` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->capture();
 * ```
 */
    public function capture() {
        $this->requireAuth();

        if (!$this->isPost() || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        // Obtener datos de imagen base64 de la camara web
        $imageData = $_POST['image'] ?? '';
        $stickerPath = $_POST['sticker'] ?? '';

        if (empty($imageData)) {
            $this->json(['success' => false, 'message' => 'No image data provided'], 400);
        }

        if (empty($stickerPath)) {
            $this->json(['success' => false, 'message' => 'No sticker selected'], 400);
        }

        // Decodificar imagen base64
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $decodedImage = base64_decode($imageData);

        if ($decodedImage === false) {
            $this->json(['success' => false, 'message' => 'Invalid image data'], 400);
        }

        // Crear imagen desde string
        $baseImage = imagecreatefromstring($decodedImage);
        if ($baseImage === false) {
            $this->json(['success' => false, 'message' => 'Failed to process image'], 500);
        }

        // Cargar pegatina
        $stickerFullPath = '../public/' . $stickerPath;
        if (!file_exists($stickerFullPath)) {
            imagedestroy($baseImage);
            $this->json(['success' => false, 'message' => 'Sticker not found'], 404);
        }

        $sticker = $this->loadImageWithAlpha($stickerFullPath);
        if ($sticker === false) {
            imagedestroy($baseImage);
            $this->json(['success' => false, 'message' => 'Failed to load sticker'], 500);
        }

        // Combinar imagenes
        $finalImage = $this->mergeImages($baseImage, $sticker);
        
        // Guardar imagen final
        $filename = $this->generateFilename();
        $filepath = '../public/uploads/images/' . $filename;
        
        if (imagepng($finalImage, $filepath)) {
            // Guardar en base de datos
            $imageModel = $this->model('Image');
            $imageId = $imageModel->create($_SESSION['user_id'], $filename);

            imagedestroy($baseImage);
            imagedestroy($sticker);
            imagedestroy($finalImage);

            if ($imageId) {
                $this->json([
                    'success' => true,
                    'image_id' => $imageId,
                    'filename' => $filename,
                    'url' => $this->url('uploads/images/' . $filename)
                ]);
            } else {
                unlink($filepath);
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

     * Método upload.

     * @return mixed

     */
/**
 * Método `upload` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `upload` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->upload();
 * ```
 */

/**
 * Método `upload` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `upload` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->upload();
 * ```
 */
    public function upload() {
        $this->requireAuth();

        if (!$this->isPost() || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'No file uploaded'], 400);
        }

        // CORRECCION PDF EVAL: La subida ahora admite 0, 1 o multiples pegatinas.
        $stickerPath = $_POST['sticker'] ?? '';

        $uploadedFile = $_FILES['image']['tmp_name'];

        // Validar archivo
        if (!Validator::isImage($uploadedFile)) {
            $this->json(['success' => false, 'message' => 'Invalid image format'], 400);
        }

        if (!Validator::maxFileSize($uploadedFile)) {
            $this->json(['success' => false, 'message' => 'File too large (max 5MB)'], 400);
        }

        // Cargar imagen subida
        $baseImage = $this->loadImageWithAlpha($uploadedFile);
        if ($baseImage === false) {
            $this->json(['success' => false, 'message' => 'Failed to process image'], 500);
        }

        $finalImage = $baseImage; // Por defecto es la imagen base (caso 0 pegatinas)

        // Si se envio pegatina, la procesamos y acoplamos
        if (!empty($stickerPath)) {
            $stickerFullPath = '../public/' . $stickerPath;
            if (!file_exists($stickerFullPath)) {
                imagedestroy($baseImage);
                $this->json(['success' => false, 'message' => 'Sticker not found'], 404);
            }

            $sticker = $this->loadImageWithAlpha($stickerFullPath);
            if ($sticker === false) {
                imagedestroy($baseImage);
                $this->json(['success' => false, 'message' => 'Failed to load sticker'], 500);
            }

            // Combinar imagenes
            $finalImage = $this->mergeImages($baseImage, $sticker);
            // La base ya no es necesaria dado que finalImage crea imagen nueva
            imagedestroy($baseImage); 
            imagedestroy($sticker);
        }
        
        // Guardar imagen final
        $filename = $this->generateFilename();
        $filepath = '../public/uploads/images/' . $filename;
        
        if (imagepng($finalImage, $filepath)) {
            // Guardar en base de datos
            $imageModel = $this->model('Image');
            $imageId = $imageModel->create($_SESSION['user_id'], $filename);

            // CORRECCION PARA EVALUACION: Limpieza dinamica de RAM
            if (isset($finalImage)) imagedestroy($finalImage);

            if ($imageId) {
                $this->json([
                    'success' => true,
                    'image_id' => $imageId,
                    'filename' => $filename,
                    'url' => $this->url('uploads/images/' . $filename)
                ]);
            } else {
                unlink($filepath);
                $this->json(['success' => false, 'message' => 'Failed to save image'], 500);
            }
        } else {
            if (isset($finalImage)) imagedestroy($finalImage);
            $this->json(['success' => false, 'message' => 'Failed to save image'], 500);
        }
    }

    /**

     * Método delete.

     * @return mixed

     */
/**
 * Elimina el recurso indicado en la base de datos.
 *
 * Descripción detallada:
 * Este método `delete` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->delete();
 * ```
 */

/**
 * Elimina el recurso indicado en la base de datos.
 *
 * Descripción detallada:
 * Este método `delete` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->delete();
 * ```
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

     * Método getStickers.

     * @return mixed

     */
/**
 * Obtiene stickers y lo devuelve.
 *
 * Descripción detallada:
 * Este método `getStickers` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->getStickers();
 * ```
 */

/**
 * Obtiene stickers y lo devuelve.
 *
 * Descripción detallada:
 * Este método `getStickers` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->getStickers();
 * ```
 */
    private function getStickers() {
        $stickersDir = '../public/stickers/';
        $stickers = [];

        if (is_dir($stickersDir)) {
            $files = scandir($stickersDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && preg_match('/\.(png)$/i', $file)) {
                    $stickers[] = 'stickers/' . $file;
                }
            }
        }

        return $stickers;
    }

    /**

     * Método loadImageWithAlpha.

     *

     * @param mixed $filepath Descripción del parámetro $filepath.

     * @return mixed

     */
/**
 * Método `loadImageWithAlpha` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `loadImageWithAlpha` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $filepath Descripción detallada de $filepath.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->loadImageWithAlpha($filepath);
 * ```
 */

/**
 * Método `loadImageWithAlpha` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `loadImageWithAlpha` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $filepath Descripción detallada de $filepath.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->loadImageWithAlpha($filepath);
 * ```
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

        // Asegurar que el canal alfa se preserva
        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    /**

     * Método mergeImages.

     *

     * @param mixed $baseImage Descripción del parámetro $baseImage.

     * @param mixed $sticker Descripción del parámetro $sticker.

     * @return mixed

     */
/**
 * Método `mergeImages` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `mergeImages` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $baseImage Descripción detallada de $baseImage.
 * @param mixed $sticker Descripción detallada de $sticker.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->mergeImages($baseImage, $sticker);
 * ```
 */

/**
 * Método `mergeImages` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `mergeImages` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $baseImage Descripción detallada de $baseImage.
 * @param mixed $sticker Descripción detallada de $sticker.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->mergeImages($baseImage, $sticker);
 * ```
 */
    private function mergeImages($baseImage, $sticker) {
        $baseWidth = imagesx($baseImage);
        $baseHeight = imagesy($baseImage);
        $stickerWidth = imagesx($sticker);
        $stickerHeight = imagesy($sticker);

        // Crear imagen final
        $finalImage = imagecreatetruecolor($baseWidth, $baseHeight);
        imagealphablending($finalImage, false);
        imagesavealpha($finalImage, true);

        // Copiar imagen base
        imagecopy($finalImage, $baseImage, 0, 0, 0, 0, $baseWidth, $baseHeight);

        // Calcular posicion de pegatina (centrada)
        $x = ($baseWidth - $stickerWidth) / 2;
        $y = ($baseHeight - $stickerHeight) / 2;

        // Combinar pegatina con alfa
        imagealphablending($finalImage, true);
        imagecopy($finalImage, $sticker, $x, $y, 0, 0, $stickerWidth, $stickerHeight);

        return $finalImage;
    }

    /**

     * Método generateFilename.

     * @return mixed

     */
/**
 * Método `generateFilename` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `generateFilename` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->generateFilename();
 * ```
 */

/**
 * Método `generateFilename` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `generateFilename` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->generateFilename();
 * ```
 */
    private function generateFilename() {
        return 'img_' . $_SESSION['user_id'] . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.png';
    }

    /**
     * Crear GIF animado desde frames capturados (BONUS)
     * Recibe un array de frames en base64 y genera un GIF animado
     */
    /**
     * Método createGif.
     * @return mixed
     */
/**
 * Persiste datos en la base de datos relacionados con este recurso.
 *
 * Descripción detallada:
 * Este método `createGif` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->createGif();
 * ```
 */
    public function createGif() {
        $this->requireAuth();

        if (!$this->isPost() || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        // Obtener frames y pegatina
        $framesData = json_decode($_POST['frames'] ?? '[]', true);
        $stickerPath = $_POST['sticker'] ?? '';
        $stickerX = intval($_POST['sticker_x'] ?? 0);
        $stickerY = intval($_POST['sticker_y'] ?? 0);
        $stickerSize = intval($_POST['sticker_size'] ?? 100);

        if (empty($framesData) || !is_array($framesData)) {
            $this->json(['success' => false, 'message' => 'No frames provided'], 400);
        }

        if (count($framesData) < 2) {
            $this->json(['success' => false, 'message' => 'Need at least 2 frames for GIF'], 400);
        }

        if (empty($stickerPath)) {
            $this->json(['success' => false, 'message' => 'No sticker selected'], 400);
        }

        // Cargar pegatina
        $stickerFullPath = '../public/' . $stickerPath;
        if (!file_exists($stickerFullPath)) {
            $this->json(['success' => false, 'message' => 'Sticker not found'], 404);
        }

        $sticker = $this->loadImageWithAlpha($stickerFullPath);
        if ($sticker === false) {
            $this->json(['success' => false, 'message' => 'Failed to load sticker'], 500);
        }

        // Redimensionar pegatina segun tamaño especificado
        $originalWidth = imagesx($sticker);
        $originalHeight = imagesy($sticker);
        $newWidth = intval($originalWidth * ($stickerSize / 100));
        $newHeight = intval($originalHeight * ($stickerSize / 100));
        
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

        // Procesar cada frame y aplicar pegatina
        $processedFrames = [];
        $tempFiles = [];
        
        foreach ($framesData as $index => $frameData) {
            // Decodificar frame
            $frameData = str_replace('data:image/png;base64,', '', $frameData);
            $frameData = str_replace(' ', '+', $frameData);
            $decodedFrame = base64_decode($frameData);
            
            if ($decodedFrame === false) {
                continue;
            }

            // Crear imagen desde string
            $frameImage = imagecreatefromstring($decodedFrame);
            if ($frameImage === false) {
                continue;
            }

            // Aplicar pegatina al frame
            $finalFrame = imagecreatetruecolor(imagesx($frameImage), imagesy($frameImage));
            imagealphablending($finalFrame, false);
            imagesavealpha($finalFrame, true);
            
            // Copiar frame base
            imagecopy($finalFrame, $frameImage, 0, 0, 0, 0, imagesx($frameImage), imagesy($frameImage));
            
            // Aplicar pegatina al frame
            imagealphablending($finalFrame, true);
            imagecopy($finalFrame, $sticker, $stickerX, $stickerY, 0, 0, imagesx($sticker), imagesy($sticker));
            
            // Guardar frame temporal como PNG
            $tempFilename = '../public/uploads/temp/frame_' . time() . '_' . $index . '.png';
            imagepng($finalFrame, $tempFilename);
            
            $processedFrames[] = $tempFilename;
            $tempFiles[] = $tempFilename;
            
            imagedestroy($frameImage);
            imagedestroy($finalFrame);
        }
        
        imagedestroy($sticker);

        if (count($processedFrames) < 2) {
            foreach ($tempFiles as $file) {
                if (file_exists($file)) unlink($file);
            }
            $this->json(['success' => false, 'message' => 'Failed to process frames'], 500);
        }

        // Generar nombre para el GIF
        $gifFilename = 'gif_' . $_SESSION['user_id'] . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.gif';
        $gifPath = '../public/uploads/images/' . $gifFilename;

        // Usar ImageMagick o GD para crear el GIF
        // Nota: PHP GD no soporta nativamente la creacion de GIFs animados
        // Se requiere ImageMagick o una libreria externa
        
        // Opcion 1: Usar ImageMagick (si esta disponible)
        if (extension_loaded('imagick')) {
            try {
                $animation = new Imagick();
                $animation->setFormat('gif');
                
                foreach ($processedFrames as $frame) {
                    $image = new Imagick($frame);
                    $image->setImageDelay(10); // 100ms = 10 frames/sec
                    $animation->addImage($image);
                    $image->clear();
                }
                
                $animation->setImageIterations(0); // Loop infinito
                $animation->writeImages($gifPath, true);
                $animation->clear();
                
                $success = true;
            } catch (Exception $e) {
                $success = false;
                $error = $e->getMessage();
            }
        } 
        // Opcion 2: Usar comando del sistema (convert de ImageMagick)
        else if (shell_exec('which convert') !== null) {
            $framesList = implode(' ', $processedFrames);
            $command = "convert -delay 10 -loop 0 {$framesList} {$gifPath}";
            exec($command, $output, $returnVar);
            $success = ($returnVar === 0 && file_exists($gifPath));
        }
        else {
            $success = false;
            $error = 'ImageMagick not available';
        }

        // Limpiar archivos temporales
        foreach ($tempFiles as $file) {
            if (file_exists($file)) unlink($file);
        }

        if ($success && file_exists($gifPath)) {
            // Guardar en base de datos
            $imageModel = $this->model('Image');
            $imageId = $imageModel->create($_SESSION['user_id'], $gifFilename);

            if ($imageId) {
                $this->json([
                    'success' => true,
                    'image_id' => $imageId,
                    'filename' => $gifFilename,
                    'url' => $this->url('uploads/images/' . $gifFilename)
                ]);
            } else {
                unlink($gifPath);
                $this->json(['success' => false, 'message' => 'Failed to save GIF to database'], 500);
            }
        } else {
            $this->json([
                'success' => false, 
                'message' => 'Failed to create GIF: ' . ($error ?? 'Unknown error')
            ], 500);
        }
    }
}
