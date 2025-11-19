-- Création de la base de données
CREATE DATABASE IF NOT EXISTS cash;

USE cash;

-- Suppression des tables si elles existent
DROP TABLE IF EXISTS caisse_history;
DROP TABLE IF EXISTS caisse_state;
DROP TABLE IF EXISTS users;

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=INNODB;

-- Insertion des utilisateurs par défaut
INSERT INTO users (email, password, role) VALUES
('user1@cash.com', '12345', 'user'),
('user2@cash.com', '12345', 'user'),
('admin@cash.com', '123456', 'admin');

-- Table pour l'état actuel de la caisse
CREATE TABLE caisse_state (
    id INT AUTO_INCREMENT PRIMARY KEY,
    billet_500 INT DEFAULT 0,
    billet_200 INT DEFAULT 0,
    billet_100 INT DEFAULT 0,
    billet_50 INT DEFAULT 0,
    billet_20 INT DEFAULT 0,
    billet_10 INT DEFAULT 0,
    billet_5 INT DEFAULT 0,
    piece_2 INT DEFAULT 0,
    piece_1 INT DEFAULT 0,
    piece_050 INT DEFAULT 0,
    piece_020 INT DEFAULT 0,
    piece_010 INT DEFAULT 0,
    piece_005 INT DEFAULT 0,
    piece_002 INT DEFAULT 0,
    piece_001 INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,
    FOREIGN KEY (updated_by) REFERENCES users(id)
) ENGINE=INNODB;

-- Insertion de l'état initial de la caisse
INSERT INTO caisse_state (
    billet_500, billet_200, billet_100, billet_50, billet_20, billet_10, billet_5,
    piece_2, piece_1, piece_050, piece_020, piece_010, piece_005, piece_002, piece_001
) VALUES (
    1, 2, 2, 4, 1, 23, 0,
    34, 23, 23, 80, 12, 8, 45, 12
);

-- Table historique des transactions
CREATE TABLE caisse_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    montant_du DECIMAL(10, 2) NOT NULL,
    montant_donne DECIMAL(10, 2) NOT NULL,
    montant_rendu DECIMAL(10, 2) NOT NULL,
    algorithme ENUM('glouton', 'inverse') DEFAULT 'glouton',
    valeur_preferee VARCHAR(50),
    monnaie_rendue JSON,
    caisse_avant JSON,
    caisse_apres JSON,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=INNODB;

