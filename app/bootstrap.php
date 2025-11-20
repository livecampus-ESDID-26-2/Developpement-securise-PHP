<?php
/**
 * Bootstrap - Initialisation de l'application
 */

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définition de la racine du projet (dossier app/)
define('ROOT_PATH', __DIR__);

// Chargement de l'autoloader
require_once ROOT_PATH . '/Core/Autoloader.php';

// Enregistrement de l'autoloader PSR-4
App\Core\Autoloader::register();

// Démarrer la session via la classe Session
App\Core\Session::start();

