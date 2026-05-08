<?php

/**
 * GalleryController
 * Gestiona la galeria publica con paginacion, likes y comentarios
 */
class GalleryController extends Controller {

    /**

     * Método index.

     * @return mixed

     */

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
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 5;
        $offset = ($page - 1) * $perPage;

        $imageModel = $this->model('Image');
        $images = $imageModel->getAll($perPage, $offset);
        $totalImages = $imageModel->count();
        $totalPages = ceil($totalImages / $perPage);

        // Obtener likes y comentarios del usuario actual
        $userLikes = [];
        if (isset($_SESSION['user_id'])) {
            $likeModel = $this->model('Like');
            foreach ($images as $image) {
                if ($likeModel->hasLiked($image['id'], $_SESSION['user_id'])) {
                    $userLikes[] = $image['id'];
                }
            }
        }

        // Obtener comentarios para cada imagen
        $commentModel = $this->model('Comment');
        $imageComments = [];
        foreach ($images as $image) {
            $imageComments[$image['id']] = $commentModel->findByImageId($image['id'], 3);
        }

        $this->view('gallery/index', [
            'images' => $images,
            'page' => $page,
            'totalPages' => $totalPages,
            'userLikes' => $userLikes,
            'comments' => $imageComments,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    /**

     * Método like.

     * @return mixed

     */

    /**

     * Método like.

     * @return mixed

     */
/**
 * Método `like` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `like` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->like();
 * ```
 */

/**
 * Método `like` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `like` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->like();
 * ```
 */
    public function like() {
        $this->requireAuth();

        if (!$this->isPost() || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $imageId = intval($_POST['image_id'] ?? 0);
        $likeModel = $this->model('Like');
        
        $success = $likeModel->toggle($imageId, $_SESSION['user_id']);
        $likeCount = $likeModel->count($imageId);
        $hasLiked = $likeModel->hasLiked($imageId, $_SESSION['user_id']);

        $this->json([
            'success' => $success,
            'like_count' => $likeCount,
            'has_liked' => $hasLiked
        ]);
    }

    /**

     * Método comment.

     * @return mixed

     */

    /**

     * Método comment.

     * @return mixed

     */
/**
 * Método `comment` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `comment` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->comment();
 * ```
 */

/**
 * Método `comment` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `comment` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->comment();
 * ```
 */
    public function comment() {
        $this->requireAuth();

        if (!$this->isPost() || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $imageId = intval($_POST['image_id'] ?? 0);
        $comment = $this->sanitize($_POST['comment'] ?? '');

        if (!Validator::required($comment)) {
            $this->json(['success' => false, 'message' => 'Comment cannot be empty'], 400);
        }

        if (!Validator::maxLength($comment, 500)) {
            $this->json(['success' => false, 'message' => 'Comment too long (max 500 characters)'], 400);
        }

        $commentModel = $this->model('Comment');
        $commentId = $commentModel->create($imageId, $_SESSION['user_id'], $comment);

        if ($commentId) {
            // Enviar notificacion al propietario de la imagen
            $imageModel = $this->model('Image');
            $image = $imageModel->findById($imageId);
            
            if ($image && $image['user_id'] != $_SESSION['user_id']) {
                $userModel = $this->model('User');
                $imageOwner = $userModel->findById($image['user_id']);
                
                if ($imageOwner && $imageOwner['notify_comments']) {
                    Mailer::sendCommentNotification(
                        $imageOwner['email'],
                        $imageOwner['username'],
                        $imageId,
                        $_SESSION['username']
                    );
                }
            }

            $this->json([
                'success' => true,
                'comment' => [
                    'id' => $commentId,
                    'username' => $_SESSION['username'],
                    'comment' => $comment,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to add comment'], 500);
        }
    }

    /**
     * Cargar más imágenes para scroll infinito (BONUS)
     * Endpoint AJAX que devuelve imágenes en formato JSON
     */
    /**
     * Método loadMore.
     * @return mixed
     */
    /**
     * Método loadMore.
     * @return mixed
     */
/**
 * Método `loadMore` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `loadMore` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->loadMore();
 * ```
 */
    public function loadMore() {
        // Obtener parametros de paginacion
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 5;
        $offset = ($page - 1) * $perPage;

        // Obtener imagenes
        $imageModel = $this->model('Image');
        $images = $imageModel->getAll($perPage, $offset);
        $totalImages = $imageModel->count();
        
        // Calcular si hay mas paginas
        $hasMore = ($offset + count($images)) < $totalImages;

        // Obtener likes del usuario actual
        $userLikes = [];
        if (isset($_SESSION['user_id'])) {
            $likeModel = $this->model('Like');
            foreach ($images as $image) {
                if ($likeModel->hasLiked($image['id'], $_SESSION['user_id'])) {
                    $userLikes[] = $image['id'];
                }
            }
        }

        // Obtener comentarios para cada imagen
        $commentModel = $this->model('Comment');
        $commentsData = [];
        foreach ($images as $image) {
            $comments = $commentModel->findByImageId($image['id'], 3);
            $commentsData[$image['id']] = $comments;
        }

        // Preparar datos para JSON
        $imagesData = [];
        foreach ($images as $image) {
            $imagesData[] = [
                'id' => $image['id'],
                'filename' => $image['filename'],
                'username' => $image['username'],
                'created_at' => $image['created_at'],
                'like_count' => $image['like_count'],
                'comment_count' => $image['comment_count'],
                'has_liked' => in_array($image['id'], $userLikes),
                'comments' => $commentsData[$image['id']] ?? []
            ];
        }

        // Devolver JSON
        $this->json([
            'success' => true,
            'images' => $imagesData,
            'page' => $page,
            'hasMore' => $hasMore,
            'csrf_token' => $this->generateCSRF()
        ]);
    }
}
