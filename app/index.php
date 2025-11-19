<?php require_once 'config_monnaie.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Caisse Enregistreuse</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 30px;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5rem;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            color: #667eea;
            font-size: 1.5rem;
            margin-bottom: 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .transaction-inputs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        input[type="number"] {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        input[type="number"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .caisse-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }

        .caisse-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .caisse-item:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .caisse-img {
            width: 100%;
            height: 60px;
            object-fit: contain;
            margin-bottom: 8px;
            border-radius: 4px;
        }

        .caisse-item label {
            display: block;
            font-size: 0.85rem;
            margin-bottom: 8px;
            color: #666;
            text-align: center;
            font-weight: 600;
        }

        .caisse-item input {
            width: 100%;
            text-align: center;
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .result-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .result-item {
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .result-item strong {
            color: #667eea;
        }

        .monnaie-rendue {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .monnaie-item {
            background: white;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            border: 2px solid #e0e0e0;
        }

        .monnaie-item.highlight {
            background: #667eea;
            color: white;
            border-color: #667eea;
            font-weight: 600;
        }

        .error {
            background: #fee;
            border-left: 4px solid #f44336;
            padding: 15px;
            border-radius: 8px;
            color: #d32f2f;
            margin-top: 20px;
        }

        .success {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            border-radius: 8px;
            color: #2e7d32;
            margin-top: 20px;
        }

        .nouvelle-caisse {
            margin-top: 20px;
        }

        .nouvelle-caisse h3 {
            color: #4caf50;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>💰 Système de Caisse Enregistreuse</h1>

        <form method="POST" action="process_caisse.php">
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
