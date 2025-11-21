<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat - Système de Caisse</title>
    <link rel="stylesheet" href="/Views/style.css">
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

            <?php if (!empty($invoice_id) && !empty($invoice_number)): ?>
            <div class="section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                <h2 class="section-title" style="color: white; border-bottom-color: rgba(255,255,255,0.3);">🧾 Facture Générée</h2>
                <div style="text-align: center; padding: 20px 0;">
                    <p style="font-size: 1.2rem; margin-bottom: 20px;">
                        Facture n° <strong><?php echo htmlspecialchars($invoice_number); ?></strong>
                    </p>
                    
                    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-top: 25px;">
                        <a href="/invoice/view?id=<?php echo $invoice_id; ?>" target="_blank" class="btn" style="background: white; color: #667eea; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-weight: 600; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                            👁️ Voir la Facture
                        </a>
                        
                        <button onclick="sendInvoice(<?php echo $invoice_id; ?>, 'email')" class="btn" style="background: #4caf50; color: white; border: none; padding: 12px 24px; border-radius: 5px; font-weight: 600; cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                            ✉️ Envoyer par Email
                        </button>
                        
                        <button onclick="sendInvoice(<?php echo $invoice_id; ?>, 'print')" class="btn" style="background: #2196f3; color: white; border: none; padding: 12px 24px; border-radius: 5px; font-weight: 600; cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                            🖨️ Imprimer
                        </button>
                        
                        <button onclick="sendInvoice(<?php echo $invoice_id; ?>, 'mail')" class="btn" style="background: #ff9800; color: white; border: none; padding: 12px 24px; border-radius: 5px; font-weight: 600; cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                            📮 Envoyer par Courrier
                        </button>
                        
                        <button onclick="sendInvoice(<?php echo $invoice_id; ?>, 'all')" class="btn" style="background: #9c27b0; color: white; border: none; padding: 12px 24px; border-radius: 5px; font-weight: 600; cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                            🚀 Tout Envoyer
                        </button>
                    </div>
                    
                    <div id="invoice-status" style="margin-top: 20px; padding: 10px; border-radius: 5px; display: none;"></div>
                </div>
            </div>
            <?php endif; ?>

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
    
    <script>
    function sendInvoice(invoiceId, method) {
        const statusDiv = document.getElementById('invoice-status');
        statusDiv.style.display = 'block';
        statusDiv.style.background = '#fff3cd';
        statusDiv.style.color = '#856404';
        statusDiv.innerHTML = '⏳ Envoi en cours...';
        
        // Créer FormData
        const formData = new FormData();
        formData.append('invoice_id', invoiceId);
        formData.append('send_method', method);
        
        // Envoyer la requête
        fetch('/invoice/send', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusDiv.style.background = '#d4edda';
                statusDiv.style.color = '#155724';
                statusDiv.innerHTML = '✅ ' + data.message;
                
                // Afficher les logs si disponibles
                if (data.logs && data.logs.length > 0) {
                    const logsHtml = '<div style="margin-top: 10px; text-align: left; font-size: 0.9rem;">' +
                        '<strong>Logs :</strong><ul style="margin: 5px 0; padding-left: 20px;">' +
                        data.logs.map(log => '<li>' + log + '</li>').join('') +
                        '</ul></div>';
                    statusDiv.innerHTML += logsHtml;
                }
            } else {
                statusDiv.style.background = '#f8d7da';
                statusDiv.style.color = '#721c24';
                statusDiv.innerHTML = '❌ ' + data.message;
            }
        })
        .catch(error => {
            statusDiv.style.background = '#f8d7da';
            statusDiv.style.color = '#721c24';
            statusDiv.innerHTML = '❌ Erreur lors de l\'envoi : ' + error.message;
        });
    }
    </script>
</body>
</html>

