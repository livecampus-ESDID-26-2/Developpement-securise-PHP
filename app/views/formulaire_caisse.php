<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Caisse Enregistreuse</title>
    <link rel="stylesheet" href="views/style.css">
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>💰 Système de Caisse Enregistreuse</h1>
            <a href="backend/historique.php" class="btn-historique">📜 Historique complet (<?php echo $total_transactions; ?>)</a>
        </div>

        <form method="POST" action="backend/traitement_caisse.php">
            <div class="section">
                <h2 class="section-title">Transaction</h2>
                <div class="transaction-inputs">
                    <div class="input-group">
                        <label for="montant_du">Montant dû (€) :</label>
                        <input type="number" id="montant_du" name="montant_du" step="0.01" value="33.48" required>
                    </div>
                    <div class="input-group">
                        <label for="montant_donne">Montant donné (€) :</label>
                        <input type="number" id="montant_donne" name="montant_donne" step="0.01" value="50.00" required>
                    </div>
                    <div class="input-group">
                        <label for="algorithme">Algorithme de rendu :</label>
                        <select id="algorithme" name="algorithme" required>
                            <option value="glouton">Standard - Plus grandes valeurs d'abord (optimal)</option>
                            <option value="inverse">Inversé - Plus petites valeurs d'abord</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="valeur_preferee">Valeur préférée (optionnel) :</label>
                        <select id="valeur_preferee" name="valeur_preferee">
                            <option value="">Aucune préférence</option>
                            <optgroup label="Billets">
                                <?php 
                                foreach ($monnaie_config as $cle => $config): 
                                    if (strpos($cle, 'billet_') === 0):
                                ?>
                                    <option value="<?php echo $cle; ?>"><?php echo $config['label']; ?></option>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </optgroup>
                            <optgroup label="Pièces">
                                <?php 
                                foreach ($monnaie_config as $cle => $config): 
                                    if (strpos($cle, 'piece_') === 0):
                                ?>
                                    <option value="<?php echo $cle; ?>"><?php echo $config['label']; ?></option>
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
                    // Les valeurs $valeurs_initiales sont définies dans systeme_caisse.php
                    foreach ($monnaie_config as $cle => $config): 
                        if (strpos($cle, 'billet_') === 0):
                    ?>
                        <div class="caisse-item-form">
                            <img src="<?php echo htmlspecialchars($config['img']); ?>" alt="<?php echo htmlspecialchars($config['label']); ?>" class="caisse-img">
                            <label><?php echo $config['label']; ?></label>
                            <input type="number" name="<?php echo $cle; ?>" value="<?php echo $valeurs_initiales[$cle]; ?>" min="0" required>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>

                <h3 style="color: #666; margin: 20px 0 15px 0;">Pièces</h3>
                <div class="caisse-grid">
                    <?php 
                    // Les valeurs $valeurs_initiales_pieces sont définies dans systeme_caisse.php
                    foreach ($monnaie_config as $cle => $config): 
                        if (strpos($cle, 'piece_') === 0):
                    ?>
                        <div class="caisse-item-form">
                            <img src="<?php echo htmlspecialchars($config['img']); ?>" alt="<?php echo htmlspecialchars($config['label']); ?>" class="caisse-img">
                            <label><?php echo $config['label']; ?></label>
                            <input type="number" name="<?php echo $cle; ?>" value="<?php echo $valeurs_initiales_pieces[$cle]; ?>" min="0" required>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>

            <button type="submit" class="submit-btn">Calculer la Monnaie</button>
        </form>

        <?php if ($dernieres_transactions && count($dernieres_transactions) > 0): ?>
        <div class="section" style="margin-top: 30px;">
            <h2 class="section-title">📊 Dernières Transactions</h2>
            <div class="historique-apercu">
                <?php foreach ($dernieres_transactions as $transaction): ?>
                <div class="transaction-card">
                    <div class="transaction-header">
                        <span class="transaction-date">
                            📅 <?php echo date('d/m/Y à H:i', strtotime($transaction['transaction_date'])); ?>
                        </span>
                        <span class="transaction-algo">
                            <?php echo $transaction['algorithme'] === 'glouton' ? '⚡ Standard' : '🔄 Inversé'; ?>
                        </span>
                    </div>
                    <div class="transaction-body">
                        <div class="transaction-montants">
                            <div class="montant-item">
                                <span class="label">Dû:</span>
                                <span class="value"><?php echo number_format($transaction['montant_du'], 2, ',', ' '); ?>€</span>
                            </div>
                            <div class="montant-item">
                                <span class="label">Donné:</span>
                                <span class="value"><?php echo number_format($transaction['montant_donne'], 2, ',', ' '); ?>€</span>
                            </div>
                            <div class="montant-item rendu">
                                <span class="label">Rendu:</span>
                                <span class="value"><?php echo number_format($transaction['montant_rendu'], 2, ',', ' '); ?>€</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php if ($total_transactions > 5): ?>
            <div style="text-align: center; margin-top: 20px;">
                <a href="backend/historique.php" class="btn-voir-plus">Voir toutes les transactions →</a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

