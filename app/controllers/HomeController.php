<?php

/**
 * HomeController
 * Handles the home page
 */
class HomeController extends Controller {
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
        $this->view('home/index');
    }
}
