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
    <link rel="stylesheet" href="/views/style.css">
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
</body>
</html>

