<?php
/**
 * Vue: Détail d'un utilisateur (admin)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail Utilisateur - Admin</title>
    <link rel="stylesheet" href="/views/style.css">
</head>
<body>
    <div class="container">
        <div class="header-bar admin">
            <div class="header-left">
                <h1>👤 Détail de l'utilisateur</h1>
                <p class="user-info">Admin: <strong><?php echo htmlspecialchars($user['email']); ?></strong></p>
            </div>
            <div class="header-right">
                <a href="/admin/dashboard" class="btn-historique">← Dashboard</a>
                <a href="/logout" class="btn-logout">🚪 Déconnexion</a>
            </div>
        </div>

        <!-- Informations de l'utilisateur -->
        <div class="user-info-section">
            <h2><?php echo htmlspecialchars($target_user['email']); ?></h2>
            <p>Inscrit le: <?php echo date('d/m/Y à H:i:s', strtotime($target_user['created_at'])); ?></p>
            <p>Rôle: <span class="user-role-badge <?php echo $target_user['role']; ?>">
                <?php echo $target_user['role'] === 'admin' ? '👨‍💼 Admin' : '👤 User'; ?>
            </span></p>
        </div>

        <!-- Statistiques de l'utilisateur -->
        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-label">Total transactions</span>
                <span class="stat-value"><?php echo $stats['total_transactions'] ?? 0; ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Total rendu</span>
                <span class="stat-value"><?php echo number_format($stats['total_returned'] ?? 0, 2, ',', ' '); ?>€</span>
            </div>
        </div>

        <!-- Historique complet de l'utilisateur -->
        <?php if ($transactions && count($transactions) > 0): ?>
        <div class="historique-complet">
            <h3>📋 Historique complet des transactions</h3>
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
            <p>📭 Aucune transaction enregistrée pour cet utilisateur.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

