<?php

namespace App\Models;

use App\Core\Model;

/**
 * Modèle Transaction - Gestion de l'historique des transactions
 */
class Transaction extends Model
{
    protected string $table = 'transaction_history';
    
    /**
     * Enregistrer une transaction
     * @param array $data Données de la transaction
     * @return int|null ID de la transaction créée ou null si échec
     */
    public function save(array $data): ?int
    {
        try {
            $sql = "INSERT INTO {$this->table} (
                amount_due, amount_given, amount_returned, algorithm, preferred_value,
                change_returned, register_before, register_after, user_id
            ) VALUES (
                :amount_due, :amount_given, :amount_returned, :algorithm, :preferred_value,
                :change_returned, :register_before, :register_after, :user_id
            )";
            
            $stmt = $this->db->prepare($sql);
            
            // Conversion des arrays en JSON
            $params = [
                'amount_due' => $data['amount_due'],
                'amount_given' => $data['amount_given'],
                'amount_returned' => $data['amount_returned'],
                'algorithm' => $data['algorithm'] ?? 'greedy',
                'preferred_value' => $data['preferred_value'] ?? null,
                'change_returned' => json_encode($data['change_returned']),
                'register_before' => json_encode($data['register_before']),
                'register_after' => json_encode($data['register_after']),
                'user_id' => $data['user_id'] ?? null
            ];
            
            if ($stmt->execute($params)) {
                return (int) $this->db->lastInsertId();
            }
            
            return null;
        } catch (\PDOException $e) {
            error_log("Erreur lors de l'enregistrement de la transaction : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Récupérer l'historique des transactions
     * @param int $limit Nombre de transactions à récupérer (0 = toutes)
     * @param int $offset Décalage pour la pagination
     * @param int|null $userId ID de l'utilisateur (null = tous)
     * @return array Liste des transactions
     */
    public function getHistory(int $limit = 0, int $offset = 0, ?int $userId = null): array
    {
        try {
            $sql = "SELECT * FROM {$this->table}";
            
            if ($userId !== null) {
                $sql .= " WHERE user_id = :user_id";
            }
            
            $sql .= " ORDER BY transaction_date DESC";
            
            if ($limit > 0) {
                $sql .= " LIMIT :limit OFFSET :offset";
            }
            
            $stmt = $this->db->prepare($sql);
            
            if ($userId !== null) {
                $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
            }
            
            if ($limit > 0) {
                $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $transactions = $stmt->fetchAll();
            
            // Décoder les JSON
            foreach ($transactions as &$transaction) {
                $transaction['change_returned'] = json_decode($transaction['change_returned'], true);
                $transaction['register_before'] = json_decode($transaction['register_before'], true);
                $transaction['register_after'] = json_decode($transaction['register_after'], true);
            }
            
            return $transactions;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de l'historique : " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Compter le nombre total de transactions
     * @param int|null $userId ID de l'utilisateur (null = tous)
     * @return int Nombre de transactions
     */
    public function count(?int $userId = null): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            
            if ($userId !== null) {
                $sql .= " WHERE user_id = :user_id";
            }
            
            $stmt = $this->db->prepare($sql);
            
            if ($userId !== null) {
                $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ? (int)$result['total'] : 0;
        } catch (\PDOException $e) {
            error_log("Erreur lors du comptage des transactions : " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Récupérer les statistiques d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array Statistiques
     */
    public function getUserStats(int $userId): array
    {
        try {
            $sql = "SELECT 
                COUNT(*) as total_transactions,
                SUM(amount_returned) as total_returned
            FROM {$this->table}
            WHERE user_id = :user_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetch() ?: [
                'total_transactions' => 0,
                'total_returned' => 0
            ];
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des statistiques : " . $e->getMessage());
            return [
                'total_transactions' => 0,
                'total_returned' => 0
            ];
        }
    }
    
    /**
     * Récupérer les statistiques globales
     * @return array Statistiques globales
     */
    public function getGlobalStats(): array
    {
        try {
            $sql = "SELECT 
                COUNT(*) as total_transactions,
                SUM(amount_returned) as total_returned,
                COUNT(DISTINCT user_id) as total_users
            FROM {$this->table}";
            
            $stmt = $this->db->query($sql);
            
            return $stmt->fetch() ?: [
                'total_transactions' => 0,
                'total_returned' => 0,
                'total_users' => 0
            ];
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des statistiques globales : " . $e->getMessage());
            return [
                'total_transactions' => 0,
                'total_returned' => 0,
                'total_users' => 0
            ];
        }
    }
}

