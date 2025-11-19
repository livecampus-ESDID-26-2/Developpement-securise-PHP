<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat - Système de Caisse</title>
    <link rel="stylesheet" href="../views/style.css">
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
                    <?php if (!empty($valeur_preferee) && isset($monnaie_config[$valeur_preferee])): ?>
                    <div class="info-item" style="border-left-color: #ff9800;">
                        <div class="info-label">Valeur préférée</div>
                        <div class="info-value" style="color: #ff9800; font-size: 1.1rem;">
                            <?php echo $monnaie_config[$valeur_preferee]['label']; ?>
                            <?php if ($monnaie_a_rendre[$valeur_preferee] > 0): ?>
                                <br><small style="font-size: 0.7rem; opacity: 0.8;">(×<?php echo $monnaie_a_rendre[$valeur_preferee]; ?> utilisées)</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">Détail de la Monnaie à Rendre</h2>
                <?php if (!empty($valeur_preferee)): ?>
                    <p style="text-align: center; color: #ff9800; font-size: 0.95rem; margin-bottom: 15px;">
                        ⭐ Valeur préférée : <strong><?php echo $monnaie_config[$valeur_preferee]['label']; ?></strong>
                    </p>
                <?php endif; ?>
                <div class="monnaie-grid">
                    <?php foreach ($monnaie_config as $cle => $config): ?>
                        <div class="monnaie-item <?php echo $monnaie_a_rendre[$cle] > 0 ? 'active' : ''; ?> <?php echo ($cle === $valeur_preferee && $monnaie_a_rendre[$cle] > 0) ? 'preferred' : ''; ?>">
                            <?php if ($monnaie_a_rendre[$cle] > 0): ?>
                                <div class="monnaie-badge <?php echo $cle === $valeur_preferee ? 'preferred-badge' : ''; ?>"><?php echo $monnaie_a_rendre[$cle]; ?></div>
                            <?php endif; ?>
                            <?php if ($cle === $valeur_preferee && $monnaie_a_rendre[$cle] > 0): ?>
                                <div style="position: absolute; top: 5px; left: 5px; font-size: 1.2rem;">⭐</div>
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
                        <?php foreach ($monnaie_config as $cle => $config): ?>
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
                                <span><?php echo $config['label']; ?></span>
                                <strong><?php echo $caisse_actuelle[$cle]; ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="caisse-column">
                        <h3>🟢 Nouvel État</h3>
                        <?php foreach ($monnaie_config as $cle => $config): ?>
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
                                <span><?php echo $config['label']; ?></span>
                                <div>
                                    <strong><?php echo $nouvelle_caisse[$cle]; ?></strong>
                                    <?php if ($caisse_actuelle[$cle] != $nouvelle_caisse[$cle]): ?>
                                        <span style="color: <?php echo $color; ?>; margin-left: 10px; font-weight: 600;">
                                            (<?php echo $difference > 0 ? '+' : ''; ?><?php echo $difference; ?>)
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="back-link">
            <a href="../index.php" class="btn">← Nouvelle Transaction</a>
        </div>
    </div>
</body>
</html>

