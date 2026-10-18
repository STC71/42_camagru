<?php

/**
 * =============================================================================
 * HomeController.php
 * =============================================================================
 * Controlador de la página de inicio de la aplicación.
 *
 * Es el punto de entrada cuando un usuario visita la raíz del sitio (/).
 * Simplemente muestra la vista home/index.
 *
 * Analogía cotidiana:
 * Es como la puerta principal de un edificio. Todo el mundo pasa primero por aquí.
 * =============================================================================
 */
class HomeController extends Controller {

    /**
     * Muestra la página de inicio
     */
    public function index() {
        $this->view('home/index');
    }
}
