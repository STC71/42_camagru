<?php

// Start session
session_start();

// Load configuration
require_once '../config/Config.php';
Config::load();

// Load core classes
require_once '../app/core/Database.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Router.php';
require_once '../app/core/Mailer.php';
require_once '../app/core/Validator.php';

// Initialize router and dispatch request
$router = new Router();
$router->dispatch();
