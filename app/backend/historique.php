<?php
/**
 * Page d'historique complet des transactions
 */

// Chargement de la configuration globale
if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/../config/config.php';
}

// Chargement de la configuration des monnaies
require_once CONFIG_PATH . '/monnaie.php';

// Récupération de toutes les transactions
$toutes_transactions = getTransactionHistory();
$total_transactions = countTransactions();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Transactions</title>
    <link rel="stylesheet" href="../views/style.css">
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>📜 Historique des Transactions</h1>
            <a href="../index.php" class="btn-retour">← Retour à la caisse</a>
        </div>

        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-label">Total transactions</span>
                <span class="stat-value"><?php echo $total_transactions; ?></span>
            </div>
            <?php if ($toutes_transactions && count($toutes_transactions) > 0): 
                $total_rendu = array_sum(array_column($toutes_transactions, 'montant_rendu'));
            ?>
            <div class="stat-item">
                <span class="stat-label">Total rendu</span>
                <span class="stat-value"><?php echo number_format($total_rendu, 2, ',', ' '); ?>€</span>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($toutes_transactions && count($toutes_transactions) > 0): ?>
        <div class="historique-complet">
            <?php foreach ($toutes_transactions as $index => $transaction): ?>
            <div class="transaction-detail">
                <div class="transaction-detail-header">
                    <div class="header-left">
                        <h3>Transaction #<?php echo $transaction['id']; ?></h3>
                        <span class="transaction-date">
                            📅 <?php echo date('d/m/Y à H:i:s', strtotime($transaction['transaction_date'])); ?>
                        </span>
                    </div>
                    <div class="header-right">
                        <span class="badge-algo <?php echo $transaction['algorithme']; ?>">
                            <?php echo $transaction['algorithme'] === 'glouton' ? '⚡ Algorithme Standard' : '🔄 Algorithme Inversé'; ?>
                        </span>
                        <?php if (!empty($transaction['valeur_preferee'])): ?>
                        <span class="badge-prefere">
                            ⭐ Valeur préférée: <?php echo $monnaie_config[$transaction['valeur_preferee']]['label'] ?? $transaction['valeur_preferee']; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="transaction-detail-body">
                    <div class="montants-section">
                        <div class="montant-box">
                            <span class="montant-label">Montant dû</span>
                            <span class="montant-value"><?php echo number_format($transaction['montant_du'], 2, ',', ' '); ?>€</span>
                        </div>
                        <div class="montant-box">
                            <span class="montant-label">Montant donné</span>
                            <span class="montant-value"><?php echo number_format($transaction['montant_donne'], 2, ',', ' '); ?>€</span>
                        </div>
                        <div class="montant-box highlight">
                            <span class="montant-label">Monnaie rendue</span>
                            <span class="montant-value"><?php echo number_format($transaction['montant_rendu'], 2, ',', ' '); ?>€</span>
                        </div>
                    </div>

                    <div class="monnaie-detail">
                        <h4>💵 Détail de la monnaie rendue</h4>
                        <div class="monnaie-grid">
                            <?php 
                            $monnaie_rendue = $transaction['monnaie_rendue'];
                            foreach ($monnaie_config as $cle => $config):
                                if (isset($monnaie_rendue[$cle]) && $monnaie_rendue[$cle] > 0):
                            ?>
                            <div class="monnaie-item">
                                <img src="<?php echo htmlspecialchars($config['img']); ?>" 
                                     alt="<?php echo htmlspecialchars($config['label']); ?>" 
                                     class="monnaie-img-small">
                                <span class="monnaie-label"><?php echo $config['label']; ?></span>
                                <span class="monnaie-count">×<?php echo $monnaie_rendue[$cle]; ?></span>
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
            <a href="../index.php" class="btn-primary">Effectuer une première transaction</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

