<?php

/**
 * =============================================================================
 * GalleryController.php
 * =============================================================================
 * Este controlador gestiona la galería pública de la aplicación.
 *
 * Responsabilidades principales:
 * - Mostrar todas las fotos de todos los usuarios (paginadas)
 * - Permitir dar/quitar likes (solo usuarios logueados)
 * - Permitir añadir comentarios (solo usuarios logueados)
 * - Enviar notificación por email al autor cuando recibe un comentario
 * - Soportar scroll infinito (bonus)
 *
 * Analogía cotidiana:
 * Es como la sala de exposiciones de un museo. Cualquiera puede entrar a ver
 * las obras, pero solo los socios pueden dejar comentarios o poner "me gusta".
 * =============================================================================
 */
class GalleryController extends Controller {

    /**
     * -------------------------------------------------------------------------
     * Página principal de la galería
     * -------------------------------------------------------------------------
     * Muestra 5 imágenes por página (requisito del subject).
     * También prepara la información de likes y comentarios del usuario actual.
     */
    public function index() {
        // Página actual (por defecto la 1)
        $page    = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 5;
        $offset  = ($page - 1) * $perPage;

        $imageModel = $this->model('Image');
        $images     = $imageModel->getAll($perPage, $offset);
        $totalImages = $imageModel->count();
        $totalPages  = ceil($totalImages / $perPage);

        // Si el usuario está logueado, averiguamos qué fotos ya le gustaron
        $userLikes = [];
        if (isset($_SESSION['user_id'])) {
            $likeModel = $this->model('Like');
            foreach ($images as $image) {
                if ($likeModel->exists($image['id'], $_SESSION['user_id'])) {
                    $userLikes[] = $image['id'];
                }
            }
        }

        // Cargamos los comentarios más recientes de cada imagen
        $commentModel  = $this->model('Comment');
        $imageComments = [];
        foreach ($images as $image) {
            $imageComments[$image['id']] = $commentModel->findByImageId($image['id']);
        }

        $this->view('gallery/index', [
            'images'      => $images,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'userLikes'   => $userLikes,
            'comments'    => $imageComments,
            'csrf_token'  => $this->generateCSRF()
        ]);
    }

    /**
     * -------------------------------------------------------------------------
     * Dar o quitar like a una foto (AJAX)
     * -------------------------------------------------------------------------
     * Solo usuarios logueados.
     * Devuelve JSON con el nuevo estado y el contador actualizado.
     */
    public function like() {
        $this->requireAuth();

        if (!$this->isPost() || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $imageId   = intval($_POST['image_id'] ?? 0);
        $likeModel = $this->model('Like');

        // toggle() añade el like si no existía, o lo quita si ya existía
        $result = $likeModel->toggle($imageId, $_SESSION['user_id']);

        $this->json([
            'success'    => true,
            'liked'      => $result['liked'],
            'like_count' => $result['count']
        ]);
    }

    /**
     * -------------------------------------------------------------------------
     * Añadir un comentario a una foto (AJAX)
     * -------------------------------------------------------------------------
     * - Solo usuarios logueados
     * - Máximo 500 caracteres
     * - Si el autor de la foto tiene las notificaciones activadas,
     *   le enviamos un email
     */
    public function comment() {
        $this->requireAuth();

        if (!$this->isPost() || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $imageId = intval($_POST['image_id'] ?? 0);
        $content = $this->sanitize($_POST['comment'] ?? '');

        if (!Validator::required($content)) {
            $this->json(['success' => false, 'message' => 'Comment cannot be empty'], 400);
        }

        if (!Validator::maxLength($content, 500)) {
            $this->json(['success' => false, 'message' => 'Comment too long (max 500 characters)'], 400);
        }

        $commentModel = $this->model('Comment');
        $commentId = $commentModel->create($imageId, $_SESSION['user_id'], $content);

        if (!$commentId) {
            $this->json(['success' => false, 'message' => 'Failed to add comment'], 500);
        }

        // ---------- Notificación por email ----------
        $imageModel = $this->model('Image');
        $image = $imageModel->findById($imageId);

        // Solo notificamos si:
        // 1. La imagen existe
        // 2. El autor no es el mismo que comenta
        // 3. El autor tiene las notificaciones activadas
        if ($image && $image['user_id'] != $_SESSION['user_id']) {
            $userModel  = $this->model('User');
            $imageOwner = $userModel->findById($image['user_id']);

            if ($imageOwner && !empty($imageOwner['notify_comments'])) {
                Mailer::sendCommentNotification(
                    $imageOwner['email'],
                    $imageOwner['username'],
                    $imageId,
                    $_SESSION['username']
                );
            }
        }

        // Devolvemos el comentario recién creado para que el frontend lo muestre
        $this->json([
            'success' => true,
            'comment' => [
                'id'         => $commentId,
                'username'   => $_SESSION['username'],
                'content'    => $content,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * -------------------------------------------------------------------------
     * Cargar más imágenes (Bonus: Scroll Infinito)
     * -------------------------------------------------------------------------
     * Endpoint AJAX que devuelve el siguiente bloque de imágenes en JSON.
     * Se usa junto con Intersection Observer en el frontend.
     */
    public function loadMore() {
        $page    = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 5;
        $offset  = ($page - 1) * $perPage;

        $imageModel  = $this->model('Image');
        $images      = $imageModel->getAll($perPage, $offset);
        $totalImages = $imageModel->count();
        $hasMore     = ($offset + $perPage) < $totalImages;

        // Información de likes del usuario actual (si está logueado)
        $userLikes = [];
        if (isset($_SESSION['user_id'])) {
            $likeModel = $this->model('Like');
            foreach ($images as $image) {
                if ($likeModel->exists($image['id'], $_SESSION['user_id'])) {
                    $userLikes[] = $image['id'];
                }
            }
        }

        $this->json([
            'success'   => true,
            'images'    => $images,
            'userLikes' => $userLikes,
            'hasMore'   => $hasMore,
            'nextPage'  => $page + 1
        ]);
    }
}
