<?php
/**
 * Configuration globale de l'application
 * Définit les chemins et constantes
 */

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définition de la racine du projet (dossier app/)
define('ROOT_PATH', dirname(__DIR__));

// Définition des chemins vers les dossiers
define('BACKEND_PATH', ROOT_PATH . '/backend');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('CONFIG_PATH', ROOT_PATH . '/config');

// Chargement des fonctions de base de données
require_once CONFIG_PATH . '/database.php';

// Chargement des fonctions d'authentification
require_once CONFIG_PATH . '/auth.php';

