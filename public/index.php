<?php

// Iniciar sesion
session_start();

// Cargar configuracion
require_once '../config/Config.php';
Config::load();

// Cargar clases core
require_once '../app/core/Database.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Router.php';
require_once '../app/core/Mailer.php';
require_once '../app/core/Validator.php';

// Multi-idioma (por defecto español) — no afecta a rutas ni a la lógica de negocio
require_once '../app/core/I18n.php';
I18n::init();

// Inicializar router y enviar solicitud
$router = new Router();
$router->dispatch();
