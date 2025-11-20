<?php

namespace App\Models;

use App\Core\Model;

/**
 * Modèle User - Gestion des utilisateurs
 */
class User extends Model
{
    protected string $table = 'users';
    
    /**
     * Récupérer un utilisateur par email
     * @param string $email Email de l'utilisateur
     * @return array|null Utilisateur ou null si non trouvé
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }
    
    /**
     * Vérifier les identifiants d'un utilisateur
     * @param string $email Email
     * @param string $password Mot de passe
     * @return array|null Utilisateur si les identifiants sont corrects, null sinon
     */
    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return null;
        }
        
        // NOTE: En production, utiliser password_verify()
        // if (password_verify($password, $user['password']))
        if ($user['password'] === $password) {
            // Ne pas retourner le mot de passe
            unset($user['password']);
            return $user;
        }
        
        return null;
    }
    
    /**
     * Récupérer tous les utilisateurs (sauf les admins)
     * @return array Liste des utilisateurs
     */
    public function getAllUsers(): array
    {
        $stmt = $this->db->query("SELECT id, email, role, created_at FROM users WHERE role != 'admin' ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Récupérer tous les utilisateurs sans filtrer
     * @return array Liste des utilisateurs
     */
    public function getAllWithRole(): array
    {
        $stmt = $this->db->query("SELECT id, email, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Créer un nouvel utilisateur
     * @param array $data Données de l'utilisateur
     * @return int|null ID du nouvel utilisateur ou null si échec
     */
    public function createUser(array $data): ?int
    {
        // NOTE: En production, hasher le mot de passe
        // $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $this->create($data);
    }
    
    /**
     * Vérifier si un email existe déjà
     * @param string $email Email à vérifier
     * @return bool True si existe, false sinon
     */
    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }
}

