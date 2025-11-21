<?php
/**
 * Vue: Historique des transactions (utilisateur)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Transactions</title>
    <link rel="stylesheet" href="/Views/style.css">
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>📜 Historique des Transactions</h1>
            <a href="/cash-register" class="btn-retour">← Retour à la caisse</a>
        </div>

        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-label">Total transactions</span>
                <span class="stat-value"><?php echo $total_transactions; ?></span>
            </div>
            <?php if ($transactions && count($transactions) > 0): 
                $total_returned = array_sum(array_column($transactions, 'amount_returned'));
            ?>
            <div class="stat-item">
                <span class="stat-label">Total rendu</span>
                <span class="stat-value"><?php echo number_format($total_returned, 2, ',', ' '); ?>€</span>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($transactions && count($transactions) > 0): ?>
        <div class="historique-complet">
            <?php foreach ($transactions as $transaction): ?>
            <div class="transaction-detail">
                <div class="transaction-detail-header">
                    <div class="header-left">
                        <h3>Transaction #<?php echo $transaction['id']; ?></h3>
                        <span class="transaction-date">
                            📅 <?php echo date('d/m/Y à H:i:s', strtotime($transaction['transaction_date'])); ?>
                        </span>
                    </div>
                    <div class="header-right">
                        <span class="badge-algo <?php echo $transaction['algorithm']; ?>">
                            <?php echo $transaction['algorithm'] === 'greedy' ? '⚡ Algorithme Standard' : '🔄 Algorithme Inversé'; ?>
                        </span>
                        <?php if (!empty($transaction['preferred_value'])): ?>
                        <span class="badge-prefere">
                            ⭐ Valeur préférée: <?php echo $currency_config[$transaction['preferred_value']]['label'] ?? $transaction['preferred_value']; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="transaction-detail-body">
                    <div class="montants-section">
                        <div class="montant-box">
                            <span class="montant-label">Montant dû</span>
                            <span class="montant-value"><?php echo number_format($transaction['amount_due'], 2, ',', ' '); ?>€</span>
                        </div>
                        <div class="montant-box">
                            <span class="montant-label">Montant donné</span>
                            <span class="montant-value"><?php echo number_format($transaction['amount_given'], 2, ',', ' '); ?>€</span>
                        </div>
                        <div class="montant-box highlight">
                            <span class="montant-label">Monnaie rendue</span>
                            <span class="montant-value"><?php echo number_format($transaction['amount_returned'], 2, ',', ' '); ?>€</span>
                        </div>
                    </div>

                    <div class="monnaie-detail">
                        <h4>💵 Détail de la monnaie rendue</h4>
                        <div class="monnaie-grid">
                            <?php 
                            $change_returned = $transaction['change_returned'];
                            foreach ($currency_config as $key => $config):
                                if (isset($change_returned[$key]) && $change_returned[$key] > 0):
                            ?>
                            <div class="monnaie-item">
                                <img src="<?php echo htmlspecialchars($config['img']); ?>" 
                                     alt="<?php echo htmlspecialchars($config['label']); ?>" 
                                     class="monnaie-img-small">
                                <span class="monnaie-label"><?php echo $config['label']; ?></span>
                                <span class="monnaie-count">×<?php echo $change_returned[$key]; ?></span>
                            </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>

                    <?php if (isset($invoices[$transaction['id']])): 
                        $invoice = $invoices[$transaction['id']];
                    ?>
                    <div class="invoice-section" style="margin-top: 20px; padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; color: white;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <h4 style="margin: 0 0 5px 0; color: white;">🧾 Facture</h4>
                                <p style="margin: 0; font-size: 0.9rem; opacity: 0.9;">
                                    N° <strong><?php echo htmlspecialchars($invoice->getInvoiceNumber()); ?></strong>
                                    <?php 
                                    $status_labels = [
                                        'pending' => '⏳ En attente',
                                        'sent_email' => '✉️ Envoyée par email',
                                        'sent_mail' => '📮 Envoyée par courrier',
                                        'printed' => '🖨️ Imprimée',
                                        'sent_sms' => '📱 Envoyée par SMS'
                                    ];
                                    $status = $invoice->getStatus();
                                    ?>
                                    - <?php echo $status_labels[$status] ?? $status; ?>
                                </p>
                            </div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="/invoice/view?id=<?php echo $invoice->getId(); ?>" 
                                   target="_blank" 
                                   style="background: white; color: #667eea; padding: 8px 16px; border-radius: 5px; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 5px;">
                                    👁️ Voir
                                </a>
                                <button onclick="sendInvoiceFromHistory(<?php echo $invoice->getId(); ?>, 'email', this)" 
                                        style="background: #4caf50; color: white; border: none; padding: 8px 16px; border-radius: 5px; font-weight: 600; cursor: pointer; font-size: 0.9rem;">
                                    ✉️ Email
                                </button>
                                <button onclick="sendInvoiceFromHistory(<?php echo $invoice->getId(); ?>, 'print', this)" 
                                        style="background: #2196f3; color: white; border: none; padding: 8px 16px; border-radius: 5px; font-weight: 600; cursor: pointer; font-size: 0.9rem;">
                                    🖨️ Imprimer
                                </button>
                                <button onclick="sendInvoiceFromHistory(<?php echo $invoice->getId(); ?>, 'mail', this)" 
                                        style="background: #ff9800; color: white; border: none; padding: 8px 16px; border-radius: 5px; font-weight: 600; cursor: pointer; font-size: 0.9rem;">
                                    📮 Courrier
                                </button>
                                <button onclick="sendInvoiceFromHistory(<?php echo $invoice->getId(); ?>, 'sms', this)" 
                                        style="background: #00bcd4; color: white; border: none; padding: 8px 16px; border-radius: 5px; font-weight: 600; cursor: pointer; font-size: 0.9rem;">
                                    📱 SMS
                                </button>
                            </div>
                        </div>
                        <div id="invoice-status-<?php echo $transaction['id']; ?>" 
                             style="margin-top: 10px; padding: 8px; border-radius: 5px; display: none; background: rgba(255,255,255,0.9); color: #333; font-size: 0.85rem;">
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <p>📭 Aucune transaction enregistrée pour le moment.</p>
            <a href="/cash-register" class="btn-primary">Effectuer une première transaction</a>
        </div>
        <?php endif; ?>
    </div>

    <script>
    function sendInvoiceFromHistory(invoiceId, method, button) {
        const transactionId = button.closest('.transaction-detail').querySelector('h3').textContent.match(/\d+/)[0];
        const statusDiv = document.getElementById('invoice-status-' + transactionId);
        
        // Afficher le message de chargement
        statusDiv.style.display = 'block';
        statusDiv.style.background = 'rgba(255, 243, 205, 0.9)';
        statusDiv.style.color = '#856404';
        statusDiv.innerHTML = '⏳ Envoi en cours...';
        
        // Désactiver le bouton
        button.disabled = true;
        const originalText = button.innerHTML;
        button.innerHTML = '⏳';
        
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
                statusDiv.style.background = 'rgba(212, 237, 218, 0.9)';
                statusDiv.style.color = '#155724';
                statusDiv.innerHTML = '✅ ' + data.message;
                
                // Afficher les logs si disponibles
                if (data.logs && data.logs.length > 0) {
                    const logsHtml = '<div style="margin-top: 8px; font-size: 0.8rem;">' +
                        '<strong>Logs :</strong><ul style="margin: 5px 0; padding-left: 20px;">' +
                        data.logs.map(log => '<li>' + log + '</li>').join('') +
                        '</ul></div>';
                    statusDiv.innerHTML += logsHtml;
                }
                
                // Recharger la page après 2 secondes pour mettre à jour le statut
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                statusDiv.style.background = 'rgba(248, 215, 218, 0.9)';
                statusDiv.style.color = '#721c24';
                statusDiv.innerHTML = '❌ ' + data.message;
                
                // Réactiver le bouton
                button.disabled = false;
                button.innerHTML = originalText;
            }
        })
        .catch(error => {
            statusDiv.style.background = 'rgba(248, 215, 218, 0.9)';
            statusDiv.style.color = '#721c24';
            statusDiv.innerHTML = '❌ Erreur lors de l\'envoi : ' + error.message;
            
            // Réactiver le bouton
            button.disabled = false;
            button.innerHTML = originalText;
        });
    }
    </script>
</body>
</html>

