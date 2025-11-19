<?php
/**
 * Système de Caisse Enregistreuse - Backend
 * Traite le calcul de la monnaie à rendre et met à jour l'état de caisse
 */

// Chargement de la configuration des monnaies
require_once 'config_monnaie.php';

// Vérification que la requête est bien en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Récupération des données du formulaire
$montant_du = floatval($_POST['montant_du'] ?? 0);
$montant_donne = floatval($_POST['montant_donne'] ?? 0);

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

// Calcul de la monnaie à rendre (algorithme glouton)
$monnaie_a_rendre = [];
$montant_restant = $montant_a_rendre_centimes;
$impossible = false;

foreach ($valeurs_monnaie as $cle => $valeur) {
    $monnaie_a_rendre[$cle] = 0;
    
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
    // Pour simplifier, on considère que le client donne le montant exact en billets/pièces
    // Note: Dans un vrai système, il faudrait demander comment le client paie
    $montant_recu_centimes = round($montant_donne * 100);
    
    // Ajout simplifié : on ajoute aux billets/pièces selon le montant
    // (Pour cet exercice, on ajoute de manière optimisée)
    $montant_a_ajouter = $montant_recu_centimes;
    foreach ($valeurs_monnaie as $cle => $valeur) {
        if ($montant_a_ajouter >= $valeur) {
            $nombre = intval($montant_a_ajouter / $valeur);
            $nouvelle_caisse[$cle] += $nombre;
            $montant_a_ajouter -= ($nombre * $valeur);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat - Système de Caisse</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 30px;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5rem;
        }

        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .section-title {
            color: #667eea;
            font-size: 1.5rem;
            margin-bottom: 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .info-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }

        .monnaie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }

        .monnaie-item {
            background: white;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            border: 3px solid #e0e0e0;
            transition: all 0.3s;
            position: relative;
            opacity: 0.5;
        }

        .monnaie-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
            transform: scale(1.08);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
            opacity: 1;
        }

        .monnaie-img {
            width: 100%;
            height: 100px;
            object-fit: contain;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .monnaie-label {
            font-size: 0.85rem;
            margin-bottom: 8px;
            opacity: 0.9;
            font-weight: 500;
        }

        .monnaie-value {
            font-size: 2rem;
            font-weight: 700;
        }

        .monnaie-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #f44336;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .error {
            background: #fee;
            border-left: 4px solid #f44336;
            padding: 20px;
            border-radius: 8px;
            color: #d32f2f;
            margin-bottom: 20px;
        }

        .error h2 {
            color: #d32f2f;
            margin-bottom: 15px;
        }

        .error ul {
            list-style: none;
            padding-left: 0;
        }

        .error li {
            margin-bottom: 10px;
            padding-left: 20px;
            position: relative;
        }

        .error li:before {
            content: "⚠️";
            position: absolute;
            left: 0;
        }

        .success {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 20px;
            border-radius: 8px;
            color: #2e7d32;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 600;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .caisse-comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .caisse-column h3 {
            color: #666;
            margin-bottom: 15px;
            text-align: center;
        }

        .caisse-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: white;
            border-radius: 6px;
            margin-bottom: 8px;
            border: 2px solid #e0e0e0;
        }

        .caisse-item.changed {
            border-color: #4caf50;
            background: #f1f8f4;
        }

        .caisse-item.increased {
            border-color: #4caf50;
            background: #f1f8f4;
        }

        .caisse-item.decreased {
            border-color: #f44336;
            background: #ffebee;
        }

        @media (max-width: 768px) {
            .caisse-comparison {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>💰 Résultat du Calcul</h1>

        <?php if (!empty($erreurs)): ?>
            <div class="error">
                <h2>❌ Erreur(s) détectée(s)</h2>
                <ul>
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?php echo htmlspecialchars($erreur); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="success">
                ✅ Transaction réussie !
            </div>

            <div class="section">
                <h2 class="section-title">Détails de la Transaction</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Montant dû</div>
                        <div class="info-value"><?php echo number_format($montant_du, 2, ',', ' '); ?>€</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Montant donné</div>
                        <div class="info-value"><?php echo number_format($montant_donne, 2, ',', ' '); ?>€</div>
                    </div>
                    <div class="info-item" style="border-left-color: #4caf50;">
                        <div class="info-label">Monnaie à rendre</div>
                        <div class="info-value" style="color: #4caf50;"><?php echo number_format($montant_a_rendre_centimes / 100, 2, ',', ' '); ?>€</div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">Détail de la Monnaie à Rendre</h2>
                <div class="monnaie-grid">
                    <?php foreach ($monnaie_config as $cle => $config): ?>
                        <div class="monnaie-item <?php echo $monnaie_a_rendre[$cle] > 0 ? 'active' : ''; ?>">
                            <?php if ($monnaie_a_rendre[$cle] > 0): ?>
                                <div class="monnaie-badge"><?php echo $monnaie_a_rendre[$cle]; ?></div>
                            <?php endif; ?>
                            <img src="<?php echo htmlspecialchars($config['img']); ?>" alt="<?php echo htmlspecialchars($config['label']); ?>" class="monnaie-img">
                            <div class="monnaie-label"><?php echo $config['label']; ?></div>
                            <div class="monnaie-value"><?php echo $monnaie_a_rendre[$cle]; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">État de Caisse - Avant / Après</h2>
                <div class="caisse-comparison">
                    <div class="caisse-column">
                        <h3>🔵 État Initial</h3>
                        <?php foreach ($valeurs_monnaie as $cle => $valeur): ?>
                            <?php 
                                $difference = $nouvelle_caisse[$cle] - $caisse_actuelle[$cle];
                                $class = '';
                                if ($difference > 0) {
                                    $class = 'increased';
                                } elseif ($difference < 0) {
                                    $class = 'decreased';
                                }
                            ?>
                            <div class="caisse-item <?php echo $class; ?>">
                                <span><?php echo $labels_monnaie[$cle]; ?></span>
                                <strong><?php echo $caisse_actuelle[$cle]; ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="caisse-column">
                        <h3>🟢 Nouvel État</h3>
                        <?php foreach ($valeurs_monnaie as $cle => $valeur): ?>
                            <?php 
                                $difference = $nouvelle_caisse[$cle] - $caisse_actuelle[$cle];
                                $color = $difference > 0 ? '#4caf50' : ($difference < 0 ? '#f44336' : '#666');
                                $class = '';
                                if ($difference > 0) {
                                    $class = 'increased';
                                } elseif ($difference < 0) {
                                    $class = 'decreased';
                                }
                            ?>
                            <div class="caisse-item <?php echo $class; ?>">
                                <span><?php echo $labels_monnaie[$cle]; ?></span>
                                <strong><?php echo $nouvelle_caisse[$cle]; ?></strong>
                                <?php if ($caisse_actuelle[$cle] != $nouvelle_caisse[$cle]): ?>
                                    <span style="color: <?php echo $color; ?>; margin-left: 10px; font-weight: 600;">
                                        (<?php echo $difference > 0 ? '+' : ''; ?><?php echo $difference; ?>)
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="back-link">
            <a href="index.php" class="btn">← Nouvelle Transaction</a>
        </div>
    </div>
</body>
</html>

