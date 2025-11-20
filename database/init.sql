-- Création de la base de données
CREATE DATABASE IF NOT EXISTS cash;

USE cash;

-- Suppression des tables si elles existent
DROP TABLE IF EXISTS transaction_history;
DROP TABLE IF EXISTS cash_register_state;
DROP TABLE IF EXISTS users;

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=INNODB;

-- Insertion des utilisateurs par défaut (mots de passe hashés)
-- user1@cash.com : mot de passe = 12345
-- user2@cash.com : mot de passe = 12345
-- admin@cash.com : mot de passe = 123456
INSERT INTO users (email, password, role) VALUES
('user1@cash.com', '$2y$12$6ZmYxDNzbsYfHZsieKUcE.N/ogcqHYaqfO4JTBEg2nDJJxCAc1dAS', 'user'),
('user2@cash.com', '$2y$12$tTYdqN.pUz1tnS.RFTsOg.lNjBAGGBT0rJicjAyREBG/0Dt8fEc96', 'user'),
('admin@cash.com', '$2y$12$EcDmJrQJlVZme89r0itkw.G0FMU0y1kxVW5KRAtVbLSpUXbCB6pvq', 'admin');

-- Table pour l'état actuel de la caisse
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
) ENGINE=INNODB;

-- Insertion de l'état initial de la caisse
INSERT INTO cash_register_state (
    bill_500, bill_200, bill_100, bill_50, bill_20, bill_10, bill_5,
    coin_2, coin_1, coin_050, coin_020, coin_010, coin_005, coin_002, coin_001
) VALUES (
    1, 2, 2, 4, 1, 23, 0,
    34, 23, 23, 80, 12, 8, 45, 12
);

-- Table historique des transactions
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
) ENGINE=INNODB;

