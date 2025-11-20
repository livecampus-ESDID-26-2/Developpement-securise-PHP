<?php
/**
 * Front Controller - Point d'entrée de l'application MVC
 */

// Chargement du bootstrap
require_once __DIR__ . '/bootstrap.php';

use App\Core\Router;

// Création du routeur
$router = new Router();

// Chargement des routes
require_once ROOT_PATH . '/routes.php';

// Dispatch de la requête
$router->dispatch();
