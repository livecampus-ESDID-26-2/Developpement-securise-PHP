<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat - Système de Caisse</title>
    <link rel="stylesheet" href="/views/style.css">
</head>
<body>
    <div class="container">
        <h1>💰 Résultat du Calcul</h1>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <h2>❌ Erreur(s) détectée(s)</h2>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
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
                        <div class="info-value"><?php echo number_format($amount_due, 2, ',', ' '); ?>€</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Montant donné</div>
                        <div class="info-value"><?php echo number_format($amount_given, 2, ',', ' '); ?>€</div>
                    </div>
                    <div class="info-item" style="border-left-color: #4caf50;">
                        <div class="info-label">Monnaie à rendre</div>
                        <div class="info-value" style="color: #4caf50;"><?php echo number_format($amount_to_return_cents / 100, 2, ',', ' '); ?>€</div>
                    </div>
                    <?php if (!empty($preferred_value) && isset($currency_config[$preferred_value])): ?>
                    <div class="info-item" style="border-left-color: #ff9800;">
                        <div class="info-label">Valeur préférée</div>
                        <div class="info-value" style="color: #ff9800; font-size: 1.1rem;">
                            <?php echo $currency_config[$preferred_value]['label']; ?>
                            <?php if ($change_to_return[$preferred_value] > 0): ?>
                                <br><small style="font-size: 0.7rem; opacity: 0.8;">(×<?php echo $change_to_return[$preferred_value]; ?> utilisées)</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">Détail de la Monnaie à Rendre</h2>
                <?php if (!empty($preferred_value)): ?>
                    <p style="text-align: center; color: #ff9800; font-size: 0.95rem; margin-bottom: 15px;">
                        ⭐ Valeur préférée : <strong><?php echo $currency_config[$preferred_value]['label']; ?></strong>
                    </p>
                <?php endif; ?>
                <div class="monnaie-grid">
                    <?php foreach ($currency_config as $key => $config): ?>
                        <div class="monnaie-item <?php echo $change_to_return[$key] > 0 ? 'active' : ''; ?> <?php echo ($key === $preferred_value && $change_to_return[$key] > 0) ? 'preferred' : ''; ?>">
                            <?php if ($change_to_return[$key] > 0): ?>
                                <div class="monnaie-badge <?php echo $key === $preferred_value ? 'preferred-badge' : ''; ?>"><?php echo $change_to_return[$key]; ?></div>
                            <?php endif; ?>
                            <?php if ($key === $preferred_value && $change_to_return[$key] > 0): ?>
                                <div style="position: absolute; top: 5px; left: 5px; font-size: 1.2rem;">⭐</div>
                            <?php endif; ?>
                            <img src="<?php echo htmlspecialchars($config['img']); ?>" alt="<?php echo htmlspecialchars($config['label']); ?>" class="monnaie-img">
                            <div class="monnaie-label"><?php echo $config['label']; ?></div>
                            <div class="monnaie-value"><?php echo $change_to_return[$key]; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">État de Caisse - Avant / Après</h2>
                <div class="caisse-comparison">
                    <div class="caisse-column">
                        <h3>🔵 État Initial</h3>
                        <?php foreach ($currency_config as $key => $config): ?>
                            <?php 
                                $difference = $new_register[$key] - $current_register[$key];
                                $class = '';
                                if ($difference > 0) {
                                    $class = 'increased';
                                } elseif ($difference < 0) {
                                    $class = 'decreased';
                                }
                            ?>
                            <div class="caisse-item <?php echo $class; ?>">
                                <span><?php echo $config['label']; ?></span>
                                <strong><?php echo $current_register[$key]; ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="caisse-column">
                        <h3>🟢 Nouvel État</h3>
                        <?php foreach ($currency_config as $key => $config): ?>
                            <?php 
                                $difference = $new_register[$key] - $current_register[$key];
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
                                    <strong><?php echo $new_register[$key]; ?></strong>
                                    <?php if ($current_register[$key] != $new_register[$key]): ?>
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
            <a href="/cash-register" class="btn">← Nouvelle Transaction</a>
        </div>
    </div>
</body>
</html>

