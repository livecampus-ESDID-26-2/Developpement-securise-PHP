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

// Chargement de l'autoloader Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Démarrer la session via la classe Session
App\Core\Session::start();

