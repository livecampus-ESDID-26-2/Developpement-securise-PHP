<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Classe Database - Gestion de la connexion à la base de données (Singleton)
 */
class Database
{
    private static ?PDO $instance = null;
    
    /**
     * Obtenir l'instance unique de la connexion PDO
     * @return PDO Instance de connexion PDO
     * @throws PDOException Si la connexion échoue
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::connect();
        }
        
        return self::$instance;
    }
    
    /**
     * Établir la connexion à la base de données
     * @throws PDOException Si la connexion échoue
     */
    private static function connect(): void
    {
        // Récupération des variables d'environnement
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');
        
        // Vérification que toutes les variables sont définies
        if (!$host || !$port || !$dbname || !$user || $password === false) {
            throw new PDOException("Variables d'environnement de base de données manquantes. Vérifiez votre fichier .env");
        }
        
        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            self::$instance = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            error_log("Erreur de connexion à la base de données : " . $e->getMessage());
            throw new PDOException("Impossible de se connecter à la base de données.");
        }
    }
    
    /**
     * Empêcher le clonage de l'instance
     */
    private function __clone() {}
    
    /**
     * Empêcher la désérialisation de l'instance
     */
    public function __wakeup()
    {
        throw new \Exception("Impossible de désérialiser un singleton.");
    }
}

