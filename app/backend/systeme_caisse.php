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

// Vérification de la connexion
requireLogin();

// Récupération des informations de l'utilisateur connecté
$user = getCurrentUser();

// Récupération de l'état de la caisse depuis la base de données
$caisse_depuis_db = getCaisseState();

// Récupération des 5 dernières transactions de l'utilisateur pour l'aperçu
$dernieres_transactions = getTransactionHistory(5, 0, $user['id']);
$total_transactions = countTransactions($user['id']);

// Si l'état existe en DB, l'utiliser pour les valeurs par défaut
if ($caisse_depuis_db) {
    $valeurs_initiales = [
        'billet_500' => $caisse_depuis_db['billet_500'],
        'billet_200' => $caisse_depuis_db['billet_200'],
        'billet_100' => $caisse_depuis_db['billet_100'],
        'billet_50' => $caisse_depuis_db['billet_50'],
        'billet_20' => $caisse_depuis_db['billet_20'],
        'billet_10' => $caisse_depuis_db['billet_10'],
        'billet_5' => $caisse_depuis_db['billet_5']
    ];
    
    $valeurs_initiales_pieces = [
        'piece_2' => $caisse_depuis_db['piece_2'],
        'piece_1' => $caisse_depuis_db['piece_1'],
        'piece_050' => $caisse_depuis_db['piece_050'],
        'piece_020' => $caisse_depuis_db['piece_020'],
        'piece_010' => $caisse_depuis_db['piece_010'],
        'piece_005' => $caisse_depuis_db['piece_005'],
        'piece_002' => $caisse_depuis_db['piece_002'],
        'piece_001' => $caisse_depuis_db['piece_001']
    ];
} else {
    // Valeurs par défaut si la DB n'est pas disponible
    $valeurs_initiales = [
        'billet_500' => 1,
        'billet_200' => 2,
        'billet_100' => 2,
        'billet_50' => 4,
        'billet_20' => 1,
        'billet_10' => 23,
        'billet_5' => 0
    ];
    
    $valeurs_initiales_pieces = [
        'piece_2' => 34,
        'piece_1' => 23,
        'piece_050' => 23,
        'piece_020' => 80,
        'piece_010' => 12,
        'piece_005' => 8,
        'piece_002' => 45,
        'piece_001' => 12
    ];
}

// Inclusion du template de formulaire
require_once VIEWS_PATH . '/formulaire_caisse.php';

