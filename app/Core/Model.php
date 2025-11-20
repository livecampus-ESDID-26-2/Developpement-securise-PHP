<?php

namespace App\Core;

use PDO;

/**
 * Classe Model - Classe de base pour tous les modèles
 */
abstract class Model
{
    protected PDO $db;
    protected string $table;
    
    /**
     * Constructeur - Initialise la connexion à la base de données
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupérer tous les enregistrements
     * @return array Liste des enregistrements
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }
    
    /**
     * Récupérer un enregistrement par ID
     * @param int $id ID de l'enregistrement
     * @return array|null Enregistrement ou null si non trouvé
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        
        return $result ?: null;
    }
    
    /**
     * Récupérer un enregistrement selon un critère
     * @param string $column Colonne à rechercher
     * @param mixed $value Valeur à rechercher
     * @return array|null Enregistrement ou null si non trouvé
     */
    public function findBy(string $column, $value): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value");
        $stmt->execute(['value' => $value]);
        $result = $stmt->fetch();
        
        return $result ?: null;
    }
    
    /**
     * Récupérer plusieurs enregistrements selon un critère
     * @param string $column Colonne à rechercher
     * @param mixed $value Valeur à rechercher
     * @return array Liste des enregistrements
     */
    public function findAllBy(string $column, $value): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value");
        $stmt->execute(['value' => $value]);
        return $stmt->fetchAll();
    }
    
    /**
     * Créer un nouvel enregistrement
     * @param array $data Données à insérer
     * @return int|null ID du nouvel enregistrement ou null si échec
     */
    public function create(array $data): ?int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($data)) {
            return (int) $this->db->lastInsertId();
        }
        
        return null;
    }
    
    /**
     * Mettre à jour un enregistrement
     * @param int $id ID de l'enregistrement
     * @param array $data Données à mettre à jour
     * @return bool True si succès, false sinon
     */
    public function update(int $id, array $data): bool
    {
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "$column = :$column";
        }
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE id = :id",
            $this->table,
            implode(', ', $setClause)
        );
        
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }
    
    /**
     * Supprimer un enregistrement
     * @param int $id ID de l'enregistrement
     * @return bool True si succès, false sinon
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}

