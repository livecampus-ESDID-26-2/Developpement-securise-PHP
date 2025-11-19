<?php
/**
 * Dashboard administrateur
 */

// Chargement de la configuration
if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/../config/config.php';
}

// Vérification des droits admin
requireAdmin();

// Récupération de l'utilisateur connecté
$admin = getCurrentUser();

// Chargement de la configuration des monnaies
require_once CONFIG_PATH . '/monnaie.php';

// Récupération de tous les utilisateurs
$users = getAllUsers();

// Statistiques globales
$total_transactions_globales = countTransactions();
$toutes_transactions_globales = getTransactionHistory(10); // Les 10 dernières

// Statistiques par utilisateur (seulement les users, pas les admins)
$stats_utilisateurs = [];
foreach ($users as $user) {
    // Ne pas afficher les administrateurs dans la liste
    if ($user['role'] === 'admin') {
        continue;
    }
    
    $stats_utilisateurs[$user['id']] = [
        'user' => $user,
        'total_transactions' => countTransactions($user['id']),
        'dernieres_transactions' => getTransactionHistory(3, 0, $user['id'])
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Système de Caisse</title>
    <link rel="stylesheet" href="../views/style.css">
</head>
<body>
    <div class="container">
        <div class="header-bar admin">
            <div class="header-left">
                <h1>👨‍💼 Dashboard Administrateur</h1>
                <p class="user-info">Connecté en tant que: <strong><?php echo htmlspecialchars($admin['email']); ?></strong></p>
            </div>
            <div class="header-right">
                <a href="admin_historique.php" class="btn-historique">📊 Historique Global</a>
                <a href="auth_logout.php" class="btn-logout">🚪 Déconnexion</a>
            </div>
        </div>

        <!-- Statistiques globales -->
        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-label">Total Utilisateurs</span>
                <span class="stat-value"><?php echo count($stats_utilisateurs); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Total Transactions</span>
                <span class="stat-value"><?php echo $total_transactions_globales; ?></span>
            </div>
            <?php if ($toutes_transactions_globales && count($toutes_transactions_globales) > 0): 
                $total_rendu_global = array_sum(array_column($toutes_transactions_globales, 'montant_rendu'));
            ?>
            <div class="stat-item">
                <span class="stat-label">Total Rendu (10 dernières)</span>
                <span class="stat-value"><?php echo number_format($total_rendu_global, 2, ',', ' '); ?>€</span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Liste des utilisateurs et leurs statistiques -->
        <div class="section">
            <h2 class="section-title">👥 Utilisateurs et leurs Activités</h2>
            
            <?php foreach ($stats_utilisateurs as $userId => $data): 
                $user = $data['user'];
                $userTransactions = $data['dernieres_transactions'];
            ?>
            <div class="user-card">
                <div class="user-card-header">
                    <div class="user-info-admin">
                        <span class="user-role-badge <?php echo $user['role']; ?>">
                            <?php echo $user['role'] === 'admin' ? '👨‍💼 Admin' : '👤 User'; ?>
                        </span>
                        <h3><?php echo htmlspecialchars($user['email']); ?></h3>
                        <span class="user-date">Inscrit le <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></span>
                    </div>
                    <div class="user-stats">
                        <div class="stat-box">
                            <span class="stat-number"><?php echo $data['total_transactions']; ?></span>
                            <span class="stat-text">transactions</span>
                        </div>
                        <a href="admin_user_detail.php?user_id=<?php echo $user['id']; ?>" class="btn-detail">
                            Voir détails →
                        </a>
                    </div>
                </div>

                <?php if ($userTransactions && count($userTransactions) > 0): ?>
                <div class="user-card-body">
                    <h4>📋 3 dernières transactions</h4>
                    <div class="mini-transactions">
                        <?php foreach ($userTransactions as $transaction): ?>
                        <div class="mini-transaction">
                            <span class="mini-date">
                                <?php echo date('d/m/Y H:i', strtotime($transaction['transaction_date'])); ?>
                            </span>
                            <div class="mini-montants">
                                <span>Dû: <?php echo number_format($transaction['montant_du'], 2, ',', ' '); ?>€</span>
                                <span>→</span>
                                <span>Donné: <?php echo number_format($transaction['montant_donne'], 2, ',', ' '); ?>€</span>
                                <span>→</span>
                                <span class="mini-rendu">Rendu: <?php echo number_format($transaction['montant_rendu'], 2, ',', ' '); ?>€</span>
                            </div>
                            <span class="mini-algo">
                                <?php echo $transaction['algorithme'] === 'glouton' ? '⚡' : '🔄'; ?>
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

