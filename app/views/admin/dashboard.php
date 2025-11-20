<?php
/**
 * Vue: Dashboard administrateur
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Système de Caisse</title>
    <link rel="stylesheet" href="/views/style.css">
</head>
<body>
    <div class="container">
        <div class="header-bar admin">
            <div class="header-left">
                <h1>👨‍💼 Dashboard Administrateur</h1>
                <p class="user-info">Connecté en tant que: <strong><?php echo htmlspecialchars($user['email']); ?></strong></p>
            </div>
            <div class="header-right">
                <a href="/admin/history" class="btn-historique">📊 Historique Global</a>
                <a href="/logout" class="btn-logout">🚪 Déconnexion</a>
            </div>
        </div>

        <!-- Statistiques globales -->
        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-label">Total Utilisateurs</span>
                <span class="stat-value"><?php echo count($users_data); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Total Transactions</span>
                <span class="stat-value"><?php echo $stats['total_transactions'] ?? 0; ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Total Rendu</span>
                <span class="stat-value"><?php echo number_format($stats['total_returned'] ?? 0, 2, ',', ' '); ?>€</span>
            </div>
        </div>

        <!-- Liste des utilisateurs et leurs statistiques -->
        <div class="section">
            <h2 class="section-title">👥 Utilisateurs et leurs Activités</h2>
            
            <?php foreach ($users_data as $data): 
                $current_user = $data['user'];
                $user_stats = $data['stats'];
                $last_transactions = $data['last_transactions'];
            ?>
            <div class="user-card">
                <div class="user-card-header">
                    <div class="user-info-admin">
                        <span class="user-role-badge <?php echo $current_user['role']; ?>">
                            <?php echo $current_user['role'] === 'admin' ? '👨‍💼 Admin' : '👤 User'; ?>
                        </span>
                        <h3><?php echo htmlspecialchars($current_user['email']); ?></h3>
                        <span class="user-date">Inscrit le <?php echo date('d/m/Y', strtotime($current_user['created_at'])); ?></span>
                    </div>
                    <div class="user-stats">
                        <div class="stat-box">
                            <span class="stat-number"><?php echo $user_stats['total_transactions'] ?? 0; ?></span>
                            <span class="stat-text">transactions</span>
                        </div>
                        <a href="/admin/user/<?php echo $current_user['id']; ?>" class="btn-detail">
                            Voir détails →
                        </a>
                    </div>
                </div>

                <?php if ($last_transactions && count($last_transactions) > 0): ?>
                <div class="user-card-body">
                    <h4>📋 3 dernières transactions</h4>
                    <div class="mini-transactions">
                        <?php foreach ($last_transactions as $transaction): ?>
                        <div class="mini-transaction">
                            <span class="mini-date">
                                <?php echo date('d/m/Y H:i', strtotime($transaction['transaction_date'])); ?>
                            </span>
                            <div class="mini-montants">
                                <span>Dû: <?php echo number_format($transaction['amount_due'], 2, ',', ' '); ?>€</span>
                                <span>→</span>
                                <span>Donné: <?php echo number_format($transaction['amount_given'], 2, ',', ' '); ?>€</span>
                                <span>→</span>
                                <span class="mini-rendu">Rendu: <?php echo number_format($transaction['amount_returned'], 2, ',', ' '); ?>€</span>
                            </div>
                            <span class="mini-algo">
                                <?php echo $transaction['algorithm'] === 'greedy' ? '⚡' : '🔄'; ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="user-card-body empty">
                    <p>📭 Aucune transaction pour cet utilisateur</p>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

