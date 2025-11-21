#!/usr/bin/env php
<?php
/**
 * =====================================================
 * Script d'initialisation de la base de données
 * =====================================================
 * Ce script unique remplace init.sh et init.sql
 * - Crée la structure de la base de données
 * - Crée les utilisateurs MySQL avec droits adaptés
 * - Utilise les variables d'environnement pour les mots de passe
 * - Insère les données initiales
 */

echo "🔧 Initialisation de la base de données MySQL...\n";

// Récupération des variables d'environnement
$dbHost = getenv('DB_HOST') ?: 'db';
$dbRootPassword = getenv('DB_ROOT_PASSWORD');
$dbName = getenv('DB_NAME') ?: 'cash';
$dbPassword = getenv('DB_PASSWORD');
$dbAdminPassword = getenv('DB_ADMIN_PASSWORD');

// Vérification que les variables d'environnement sont définies
$errors = [];
if (empty($dbRootPassword)) {
    $errors[] = "❌ Erreur : La variable DB_ROOT_PASSWORD n'est pas définie";
}
if (empty($dbPassword)) {
    $errors[] = "❌ Erreur : La variable DB_PASSWORD n'est pas définie";
}
if (empty($dbAdminPassword)) {
    $errors[] = "❌ Erreur : La variable DB_ADMIN_PASSWORD n'est pas définie";
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "\n";
    }
    exit(1);
}

try {
    // Connexion à MySQL (sans spécifier de base de données)
    echo "🔌 Connexion à MySQL...\n";
    $pdo = new PDO(
        "mysql:host={$dbHost}",
        'root',
        $dbRootPassword,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Création de la base de données
    echo "📊 Création de la base de données '{$dbName}'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}`");
    $pdo->exec("USE `{$dbName}`");

    // Suppression des tables si elles existent
    echo "🗑️  Suppression des tables existantes...\n";
    $pdo->exec("DROP TABLE IF EXISTS transaction_history");
    $pdo->exec("DROP TABLE IF EXISTS cash_register_state");
    $pdo->exec("DROP TABLE IF EXISTS users");

    // Création de la table users
    echo "👥 Création de la table 'users'...\n";
    $pdo->exec("
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('user', 'admin') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB
    ");

    // Insertion des utilisateurs par défaut
    echo "📝 Insertion des utilisateurs de test...\n";
    $pdo->exec("
        INSERT INTO users (email, password, role) VALUES
        ('user1@cash.com', '\$2y\$12\$6ZmYxDNzbsYfHZsieKUcE.N/ogcqHYaqfO4JTBEg2nDJJxCAc1dAS', 'user'),
        ('user2@cash.com', '\$2y\$12\$tTYdqN.pUz1tnS.RFTsOg.lNjBAGGBT0rJicjAyREBG/0Dt8fEc96', 'user'),
        ('admin@cash.com', '\$2y\$12\$EcDmJrQJlVZme89r0itkw.G0FMU0y1kxVW5KRAtVbLSpUXbCB6pvq', 'admin')
    ");
    echo "   • user1@cash.com : mot de passe = 12345\n";
    echo "   • user2@cash.com : mot de passe = 12345\n";
    echo "   • admin@cash.com : mot de passe = 123456\n";

    // Création de la table cash_register_state
    echo "💰 Création de la table 'cash_register_state'...\n";
    $pdo->exec("
        CREATE TABLE cash_register_state (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bill_500 INT DEFAULT 0,
            bill_200 INT DEFAULT 0,
            bill_100 INT DEFAULT 0,
            bill_50 INT DEFAULT 0,
            bill_20 INT DEFAULT 0,
            bill_10 INT DEFAULT 0,
            bill_5 INT DEFAULT 0,
            coin_2 INT DEFAULT 0,
            coin_1 INT DEFAULT 0,
            coin_050 INT DEFAULT 0,
            coin_020 INT DEFAULT 0,
            coin_010 INT DEFAULT 0,
            coin_005 INT DEFAULT 0,
            coin_002 INT DEFAULT 0,
            coin_001 INT DEFAULT 0,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            updated_by INT,
            FOREIGN KEY (updated_by) REFERENCES users(id)
        ) ENGINE=INNODB
    ");

    // Insertion de l'état initial de la caisse avec des valeurs par défaut
    echo "💵 Insertion de l'état initial de la caisse...\n";
    
    $pdo->exec("
        INSERT INTO cash_register_state (
            bill_500, bill_200, bill_100, bill_50, bill_20, bill_10, bill_5,
            coin_2, coin_1, coin_050, coin_020, coin_010, coin_005, coin_002, coin_001
        ) VALUES (
            2, 3, 5, 10, 20, 15, 10,
            30, 50, 40, 50, 60, 40, 30, 20
        )
    ");
    
    // Calcul du total pour l'affichage
    $total = (2*500 + 3*200 + 5*100 + 10*50 + 20*20 + 15*10 + 10*5 +
              30*2 + 50*1 + 40*0.50 + 50*0.20 + 60*0.10 + 40*0.05 + 30*0.02 + 20*0.01);
    echo "   Total en caisse : " . number_format($total, 2, ',', ' ') . " €\n";

    // Création de la table transaction_history
    echo "📜 Création de la table 'transaction_history'...\n";
    $pdo->exec("
        CREATE TABLE transaction_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            amount_due DECIMAL(10, 2) NOT NULL,
            amount_given DECIMAL(10, 2) NOT NULL,
            amount_returned DECIMAL(10, 2) NOT NULL,
            algorithm ENUM('greedy', 'reverse') DEFAULT 'greedy',
            preferred_value VARCHAR(50),
            change_returned JSON,
            register_before JSON,
            register_after JSON,
            user_id INT,
            FOREIGN KEY (user_id) REFERENCES users(id)
        ) ENGINE=INNODB
    ");

    // Création de la table invoices
    echo "🧾 Création de la table 'invoices' pour les factures...\n";
    $pdo->exec("
        CREATE TABLE invoices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            transaction_id INT NOT NULL,
            invoice_number VARCHAR(50) NOT NULL UNIQUE,
            amount_due DECIMAL(10, 2) NOT NULL,
            amount_given DECIMAL(10, 2) NOT NULL,
            amount_returned DECIMAL(10, 2) NOT NULL,
            change_returned JSON,
            invoice_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            user_id INT,
            status ENUM('pending', 'sent_email', 'sent_mail', 'printed') DEFAULT 'pending',
            FOREIGN KEY (transaction_id) REFERENCES transaction_history(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id),
            INDEX idx_invoice_number (invoice_number),
            INDEX idx_transaction_id (transaction_id),
            INDEX idx_user_id (user_id)
        ) ENGINE=INNODB
    ");

    // Création des utilisateurs MySQL
    echo "👤 Création des utilisateurs MySQL...\n";
    
    // Suppression des utilisateurs s'ils existent déjà
    $pdo->exec("DROP USER IF EXISTS 'cash_user'@'%'");
    $pdo->exec("DROP USER IF EXISTS 'cash_admin'@'%'");

    // Création de l'utilisateur STANDARD (droits limités)
    echo "   • Création de 'cash_user' avec droits limités...\n";
    $pdo->exec("CREATE USER 'cash_user'@'%' IDENTIFIED BY '{$dbPassword}'");
    
    // Droits limités pour l'utilisateur standard
    // SELECT : Lecture des données
    // INSERT : Ajout de nouvelles données
    // UPDATE : Modification des données existantes
    // Pas de DELETE ni de DROP pour éviter les suppressions accidentelles
    $pdo->exec("GRANT SELECT, INSERT, UPDATE ON `{$dbName}`.* TO 'cash_user'@'%'");

    // Création de l'utilisateur ADMIN (tous les droits)
    echo "   • Création de 'cash_admin' avec tous les droits...\n";
    $pdo->exec("CREATE USER 'cash_admin'@'%' IDENTIFIED BY '{$dbAdminPassword}'");
    
    // Tous les droits pour l'administrateur
    $pdo->exec("GRANT ALL PRIVILEGES ON `{$dbName}`.* TO 'cash_admin'@'%'");
    
    // Application des privilèges
    $pdo->exec("FLUSH PRIVILEGES");

    echo "\n✅ Base de données initialisée avec succès !\n";
    echo "   📊 Structure : Tables et données créées\n";
    echo "   👤 cash_user : Droits SELECT, INSERT, UPDATE\n";
    echo "   👨‍💼 cash_admin : Tous les droits\n";

} catch (PDOException $e) {
    echo "\n❌ Erreur lors de l'initialisation : " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n❌ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}

