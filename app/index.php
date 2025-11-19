<?php
/**
 * Point d'entrée de l'application
 * Redirige vers le système de caisse
 */

// Chargement de la configuration (définit ROOT_PATH, BACKEND_PATH, VIEWS_PATH, CONFIG_PATH)
require_once __DIR__ . '/config/config.php';

// Inclusion du système de caisse
require_once BACKEND_PATH . '/systeme_caisse.php';
