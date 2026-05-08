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

// Inicializar router y enviar solicitud
$router = new Router();
$router->dispatch();
