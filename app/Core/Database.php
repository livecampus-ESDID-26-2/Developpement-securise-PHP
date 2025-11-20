<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Classe Database - Gestion de la connexion à la base de données (Singleton)
 * 
 * Gère deux types de connexions selon les besoins :
 * - Connexion STANDARD (user) : Droits limités (SELECT, INSERT, UPDATE)
 * - Connexion ADMIN : Tous les droits (incluant DELETE, DROP)
 */
class Database
{
    private static ?PDO $instance = null;
    private static ?PDO $adminInstance = null;
    
    /**
     * Obtenir l'instance unique de la connexion PDO standard (droits limités)
     * Utilisée pour les opérations courantes de l'application
     * 
     * @return PDO Instance de connexion PDO standard
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
     * Obtenir l'instance de connexion PDO administrateur (tous les droits)
     * Utilisée uniquement pour les opérations sensibles nécessitant des droits étendus
     * 
     * ⚠️ À utiliser avec précaution uniquement pour :
     * - Opérations de maintenance
     * - Suppressions de données (DELETE)
     * - Modifications de structure (ALTER, DROP)
     * 
     * @return PDO Instance de connexion PDO admin
     * @throws PDOException Si la connexion échoue
     */
    public static function getAdminInstance(): PDO
    {
        if (self::$adminInstance === null) {
            self::connectAdmin();
        }
        
        return self::$adminInstance;
    }
    
    /**
     * Établir la connexion à la base de données (utilisateur standard)
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
            error_log("Erreur de connexion à la base de données (user) : " . $e->getMessage());
            throw new PDOException("Impossible de se connecter à la base de données.");
        }
    }
    
    /**
     * Établir la connexion à la base de données (utilisateur admin)
     * @throws PDOException Si la connexion échoue
     */
    private static function connectAdmin(): void
    {
        // Récupération des variables d'environnement
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $dbname = getenv('DB_NAME');
        $adminUser = getenv('DB_ADMIN_USER');
        $adminPassword = getenv('DB_ADMIN_PASSWORD');
        
        // Vérification que toutes les variables sont définies
        if (!$host || !$port || !$dbname || !$adminUser || $adminPassword === false) {
            throw new PDOException("Variables d'environnement admin manquantes. Vérifiez votre fichier .env");
        }
        
        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            self::$adminInstance = new PDO($dsn, $adminUser, $adminPassword, $options);
        } catch (PDOException $e) {
            error_log("Erreur de connexion à la base de données (admin) : " . $e->getMessage());
            throw new PDOException("Impossible de se connecter à la base de données (admin).");
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

