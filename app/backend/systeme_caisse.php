<?php
/**
 * 💰 Système de Caisse Enregistreuse
 * Page principale du système de caisse
 */

// Chargement de la configuration globale si pas déjà chargée
if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/../config/config.php';
}

// Chargement de la configuration des monnaies
require_once CONFIG_PATH . '/monnaie.php';

// Inclusion du template de formulaire
require_once VIEWS_PATH . '/formulaire_caisse.php';

