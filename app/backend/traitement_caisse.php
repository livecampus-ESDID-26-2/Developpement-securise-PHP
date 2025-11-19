<?php
/**
 * Backend - Traitement de la caisse enregistreuse
 * Logique métier pure sans HTML
 */

// Chargement de la configuration globale si pas déjà chargée
if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/../config/config.php';
}

// Chargement de la configuration des monnaies
require_once CONFIG_PATH . '/monnaie.php';

// Vérification que la requête est bien en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// Récupération des données du formulaire
$montant_du = floatval($_POST['montant_du'] ?? 0);
$montant_donne = floatval($_POST['montant_donne'] ?? 0);
$valeur_preferee = $_POST['valeur_preferee'] ?? '';
$algorithme = $_POST['algorithme'] ?? 'glouton';

// Pour compatibilité avec le code existant
$valeurs_monnaie = [];
$labels_monnaie = [];
foreach ($monnaie_config as $cle => $config) {
    $valeurs_monnaie[$cle] = $config['centimes'];
    $labels_monnaie[$cle] = $config['label'];
}

// Récupération de l'état de caisse actuel
$caisse_actuelle = [];
foreach ($valeurs_monnaie as $cle => $valeur) {
    $caisse_actuelle[$cle] = intval($_POST[$cle] ?? 0);
}

// Calcul du montant à rendre en centimes
$montant_a_rendre_centimes = round(($montant_donne - $montant_du) * 100);

// Validation
$erreurs = [];

if ($montant_du <= 0) {
    $erreurs[] = "Le montant dû doit être supérieur à 0.";
}

if ($montant_donne < $montant_du) {
    $erreurs[] = "Le montant donné (" . number_format($montant_donne, 2, ',', ' ') . "€) est insuffisant pour payer " . number_format($montant_du, 2, ',', ' ') . "€.";
}

// Calcul de la monnaie à rendre (algorithme avec préférence)
$monnaie_a_rendre = [];
$montant_restant = $montant_a_rendre_centimes;
$impossible = false;

// Initialiser tous les compteurs à 0
foreach ($valeurs_monnaie as $cle => $valeur) {
    $monnaie_a_rendre[$cle] = 0;
}

// Si une valeur préférée est spécifiée, essayer de maximiser son utilisation
if (!empty($valeur_preferee) && isset($valeurs_monnaie[$valeur_preferee])) {
    $valeur_pref_centimes = $valeurs_monnaie[$valeur_preferee];
    
    // Calculer combien on peut utiliser de la valeur préférée
    if ($montant_restant >= $valeur_pref_centimes && $caisse_actuelle[$valeur_preferee] > 0) {
        $nombre_max_necessaire = intval($montant_restant / $valeur_pref_centimes);
        $nombre_disponible = $caisse_actuelle[$valeur_preferee];
        $nombre_a_utiliser = min($nombre_max_necessaire, $nombre_disponible);
        
        $monnaie_a_rendre[$valeur_preferee] = $nombre_a_utiliser;
        $montant_restant -= ($nombre_a_utiliser * $valeur_pref_centimes);
    }
}

// Préparer les valeurs de monnaie selon l'algorithme choisi
$valeurs_ordonnees = $valeurs_monnaie;
if ($algorithme === 'inverse') {
    // Inverser l'ordre : du plus petit au plus grand
    $valeurs_ordonnees = array_reverse($valeurs_monnaie, true);
}

// Algorithme pour le reste (glouton ou inversé)
foreach ($valeurs_ordonnees as $cle => $valeur) {
    // Si c'est la valeur préférée, on l'a déjà traitée
    if ($cle === $valeur_preferee) {
        continue;
    }
    
    if ($montant_restant >= $valeur && $caisse_actuelle[$cle] > 0) {
        $nombre_max_necessaire = intval($montant_restant / $valeur);
        $nombre_disponible = $caisse_actuelle[$cle];
        $nombre_a_rendre = min($nombre_max_necessaire, $nombre_disponible);
        
        $monnaie_a_rendre[$cle] = $nombre_a_rendre;
        $montant_restant -= ($nombre_a_rendre * $valeur);
    }
}

// Vérification qu'on peut rendre la monnaie exacte
if ($montant_restant > 0) {
    $erreurs[] = "Impossible de rendre la monnaie exacte avec l'état actuel de la caisse. Il manque " . number_format($montant_restant / 100, 2, ',', ' ') . "€.";
    $impossible = true;
}

// Calcul du nouvel état de caisse
$nouvelle_caisse = [];
if (!$impossible && empty($erreurs)) {
    foreach ($valeurs_monnaie as $cle => $valeur) {
        // On enlève ce qu'on rend et on ajoute ce qu'on reçoit
        $nouvelle_caisse[$cle] = $caisse_actuelle[$cle] - $monnaie_a_rendre[$cle];
    }
    
    // Ajout du montant reçu du client (on décompose le montant donné)
    $montant_recu_centimes = round($montant_donne * 100);
    
    // Ajout simplifié : on ajoute aux billets/pièces selon le montant
    $montant_a_ajouter = $montant_recu_centimes;
    foreach ($valeurs_monnaie as $cle => $valeur) {
        if ($montant_a_ajouter >= $valeur) {
            $nombre = intval($montant_a_ajouter / $valeur);
            $nouvelle_caisse[$cle] += $nombre;
            $montant_a_ajouter -= ($nombre * $valeur);
        }
    }
}

// Inclusion du template de résultat
require_once VIEWS_PATH . '/resultat_caisse.php';

