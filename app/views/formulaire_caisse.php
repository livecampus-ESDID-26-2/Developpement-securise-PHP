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
        <h1>💰 Système de Caisse Enregistreuse</h1>

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
                        <label for="valeur_preferee">Valeur préférée (optionnel) :</label>
                        <select id="valeur_preferee" name="valeur_preferee">
                            <option value="">Aucune préférence (algorithme standard)</option>
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
                    $valeurs_initiales = [
                        'billet_500' => 1,
                        'billet_200' => 2,
                        'billet_100' => 2,
                        'billet_50' => 4,
                        'billet_20' => 1,
                        'billet_10' => 23,
                        'billet_5' => 0
                    ];
                    foreach ($monnaie_config as $cle => $config): 
                        if (strpos($cle, 'billet_') === 0):
                    ?>
                        <div class="caisse-item">
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
                    $valeurs_initiales_pieces = [
                        'piece_2' => 34,
                        'piece_1' => 23,
                        'piece_050' => 23,
                        'piece_020' => 80,
                        'piece_010' => 12,
                        'piece_005' => 8,
                        'piece_002' => 45,
                        'piece_001' => 12
                    ];
                    foreach ($monnaie_config as $cle => $config): 
                        if (strpos($cle, 'piece_') === 0):
                    ?>
                        <div class="caisse-item">
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
    </div>
</body>
</html>

