<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Caisse Enregistreuse</title>
    <link rel="stylesheet" href="/Views/style.css">
</head>
<body>
    <div class="container">
        <div class="header-bar">
            <div class="header-left">
                <h1>💰 Système de Caisse Enregistreuse</h1>
                <p class="user-info">👤 Connecté en tant que: <strong><?php echo htmlspecialchars($user['email']); ?></strong></p>
            </div>
            <div class="header-right">
                <a href="/cash-register/history" class="btn-historique">📜 Historique (<?php echo $total_transactions; ?>)</a>
                <a href="/logout" class="btn-logout">🚪 Déconnexion</a>
            </div>
        </div>

        <form method="POST" action="/cash-register/process">
            <div class="section">
                <h2 class="section-title">Transaction</h2>
                <div class="transaction-inputs">
                    <div class="input-group">
                        <label for="amount_due">Montant dû (€) :</label>
                        <input type="number" id="amount_due" name="amount_due" step="0.01" value="33.48" required>
                    </div>
                    <div class="input-group">
                        <label for="amount_given">Montant donné (€) :</label>
                        <input type="number" id="amount_given" name="amount_given" step="0.01" value="50.00" required>
                    </div>
                    <div class="input-group">
                        <label for="algorithm">Algorithme de rendu :</label>
                        <select id="algorithm" name="algorithm" required>
                            <option value="greedy">Standard - Plus grandes valeurs d'abord (optimal)</option>
                            <option value="reverse">Inversé - Plus petites valeurs d'abord</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="preferred_value">Valeur préférée (optionnel) :</label>
                        <select id="preferred_value" name="preferred_value">
                            <option value="">Aucune préférence</option>
                            <optgroup label="Billets">
                                <?php 
                                foreach ($currency_config as $key => $config): 
                                    if (strpos($key, 'bill_') === 0):
                                ?>
                                    <option value="<?php echo $key; ?>"><?php echo $config['label']; ?></option>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </optgroup>
                            <optgroup label="Pièces">
                                <?php 
                                foreach ($currency_config as $key => $config): 
                                    if (strpos($key, 'coin_') === 0):
                                ?>
                                    <option value="<?php echo $key; ?>"><?php echo $config['label']; ?></option>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">État de la Caisse</h2>
                
                <h3 style="color: #666; margin-bottom: 15px;">Billets</h3>
                <div class="caisse-grid">
                    <?php 
                    foreach ($currency_config as $key => $config): 
                        if (strpos($key, 'bill_') === 0):
                    ?>
                        <div class="caisse-item-form">
                            <img src="<?php echo htmlspecialchars($config['img']); ?>" alt="<?php echo htmlspecialchars($config['label']); ?>" class="caisse-img">
                            <label><?php echo $config['label']; ?></label>
                            <input type="number" name="<?php echo $key; ?>" value="<?php echo $initial_bill_values[$key]; ?>" min="0" required>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>

                <h3 style="color: #666; margin: 20px 0 15px 0;">Pièces</h3>
                <div class="caisse-grid">
                    <?php 
                    foreach ($currency_config as $key => $config): 
                        if (strpos($key, 'coin_') === 0):
                    ?>
                        <div class="caisse-item-form">
                            <img src="<?php echo htmlspecialchars($config['img']); ?>" alt="<?php echo htmlspecialchars($config['label']); ?>" class="caisse-img">
                            <label><?php echo $config['label']; ?></label>
                            <input type="number" name="<?php echo $key; ?>" value="<?php echo $initial_coin_values[$key]; ?>" min="0" required>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>

            <button type="submit" class="submit-btn">Calculer la Monnaie</button>
        </form>

        <?php if ($recent_transactions && count($recent_transactions) > 0): ?>
        <div class="section" style="margin-top: 30px;">
            <h2 class="section-title">📊 Dernières Transactions</h2>
            <div class="historique-apercu">
                <?php foreach ($recent_transactions as $transaction): ?>
                <div class="transaction-card">
                    <div class="transaction-header">
                        <span class="transaction-date">
                            📅 <?php echo date('d/m/Y à H:i', strtotime($transaction['transaction_date'])); ?>
                        </span>
                        <span class="transaction-algo">
                            <?php echo $transaction['algorithm'] === 'greedy' ? '⚡ Standard' : '🔄 Inversé'; ?>
                        </span>
                    </div>
                    <div class="transaction-body">
                        <div class="transaction-montants">
                            <div class="montant-item">
                                <span class="label">Dû:</span>
                                <span class="value"><?php echo number_format($transaction['amount_due'], 2, ',', ' '); ?>€</span>
                            </div>
                            <div class="montant-item">
                                <span class="label">Donné:</span>
                                <span class="value"><?php echo number_format($transaction['amount_given'], 2, ',', ' '); ?>€</span>
                            </div>
                            <div class="montant-item rendu">
                                <span class="label">Rendu:</span>
                                <span class="value"><?php echo number_format($transaction['amount_returned'], 2, ',', ' '); ?>€</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php if ($total_transactions > 5): ?>
            <div style="text-align: center; margin-top: 20px;">
                <a href="/cash-register/history" class="btn-voir-plus">Voir toutes les transactions →</a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

