<?php

namespace App\Models;

use App\Core\Model;
use App\Entities\Invoice as InvoiceEntity;

/**
 * Modèle Invoice - Gestion des factures
 */
class Invoice extends Model
{
    protected string $table = 'invoices';
    
    /**
     * Créer une nouvelle facture
     * @param array $data Données de la facture
     * @return int|null ID de la facture créée ou null en cas d'erreur
     */
    public function create(array $data): ?int
    {
        try {
            $sql = "INSERT INTO {$this->table} (
                transaction_id, invoice_number, amount_due, amount_given, 
                amount_returned, change_returned, user_id, status
            ) VALUES (
                :transaction_id, :invoice_number, :amount_due, :amount_given,
                :amount_returned, :change_returned, :user_id, :status
            )";
            
            $stmt = $this->db->prepare($sql);
            
            $params = [
                'transaction_id' => $data['transaction_id'],
                'invoice_number' => $data['invoice_number'] ?? InvoiceEntity::generateInvoiceNumber(),
                'amount_due' => $data['amount_due'],
                'amount_given' => $data['amount_given'],
                'amount_returned' => $data['amount_returned'],
                'change_returned' => json_encode($data['change_returned']),
                'user_id' => $data['user_id'],
                'status' => $data['status'] ?? 'pending'
            ];
            
            if ($stmt->execute($params)) {
                return (int) $this->db->lastInsertId();
            }
            
            return null;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la création de la facture : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Récupérer une facture par son ID
     * @param int $id ID de la facture
     * @return InvoiceEntity|null Entité facture ou null si non trouvée
     */
    public function findById(int $id): ?InvoiceEntity
    {
        try {
            $sql = "SELECT i.*, u.email as user_email 
                    FROM {$this->table} i 
                    LEFT JOIN users u ON i.user_id = u.id 
                    WHERE i.id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            $data = $stmt->fetch();
            
            if (!$data) {
                return null;
            }
            
            return InvoiceEntity::fromArray($data);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de la facture : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Récupérer une facture par son numéro
     * @param string $invoiceNumber Numéro de facture
     * @return InvoiceEntity|null Entité facture ou null si non trouvée
     */
    public function findByNumber(string $invoiceNumber): ?InvoiceEntity
    {
        try {
            $sql = "SELECT i.*, u.email as user_email 
                    FROM {$this->table} i 
                    LEFT JOIN users u ON i.user_id = u.id 
                    WHERE i.invoice_number = :invoice_number";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['invoice_number' => $invoiceNumber]);
            
            $data = $stmt->fetch();
            
            if (!$data) {
                return null;
            }
            
            return InvoiceEntity::fromArray($data);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de la facture : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Récupérer une facture par l'ID de transaction
     * @param int $transactionId ID de la transaction
     * @return InvoiceEntity|null Entité facture ou null si non trouvée
     */
    public function findByTransactionId(int $transactionId): ?InvoiceEntity
    {
        try {
            $sql = "SELECT i.*, u.email as user_email 
                    FROM {$this->table} i 
                    LEFT JOIN users u ON i.user_id = u.id 
                    WHERE i.transaction_id = :transaction_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['transaction_id' => $transactionId]);
            
            $data = $stmt->fetch();
            
            if (!$data) {
                return null;
            }
            
            return InvoiceEntity::fromArray($data);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de la facture : " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Récupérer toutes les factures d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @param int $limit Limite de résultats (0 = tous)
     * @param int $offset Décalage pour la pagination
     * @return array Liste d'entités Invoice
     */
    public function findByUserId(int $userId, int $limit = 0, int $offset = 0): array
    {
        try {
            $sql = "SELECT i.*, u.email as user_email 
                    FROM {$this->table} i 
                    LEFT JOIN users u ON i.user_id = u.id 
                    WHERE i.user_id = :user_id 
                    ORDER BY i.invoice_date DESC";
            
            if ($limit > 0) {
                $sql .= " LIMIT :limit OFFSET :offset";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
            
            if ($limit > 0) {
                $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $results = $stmt->fetchAll();
            
            return array_map(function($data) {
                return InvoiceEntity::fromArray($data);
            }, $results);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des factures : " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer toutes les factures
     * @param int $limit Limite de résultats (0 = tous)
     * @param int $offset Décalage pour la pagination
     * @return array Liste d'entités Invoice
     */
    public function findAll(int $limit = 0, int $offset = 0): array
    {
        try {
            $sql = "SELECT i.*, u.email as user_email 
                    FROM {$this->table} i 
                    LEFT JOIN users u ON i.user_id = u.id 
                    ORDER BY i.invoice_date DESC";
            
            if ($limit > 0) {
                $sql .= " LIMIT :limit OFFSET :offset";
            }
            
            $stmt = $this->db->prepare($sql);
            
            if ($limit > 0) {
                $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $results = $stmt->fetchAll();
            
            return array_map(function($data) {
                return InvoiceEntity::fromArray($data);
            }, $results);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des factures : " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Mettre à jour le statut d'une facture
     * @param int $id ID de la facture
     * @param string $status Nouveau statut
     * @return bool True si succès, false sinon
     */
    public function updateStatus(int $id, string $status): bool
    {
        try {
            $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                'id' => $id,
                'status' => $status
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour du statut : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Compter les factures d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return int Nombre de factures
     */
    public function countByUserId(int $userId): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            
            $result = $stmt->fetch();
            return $result ? (int)$result['total'] : 0;
        } catch (\PDOException $e) {
            error_log("Erreur lors du comptage des factures : " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Compter toutes les factures
     * @return int Nombre total de factures
     */
    public function countAll(): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $stmt = $this->db->query($sql);
            
            $result = $stmt->fetch();
            return $result ? (int)$result['total'] : 0;
        } catch (\PDOException $e) {
            error_log("Erreur lors du comptage des factures : " . $e->getMessage());
            return 0;
        }
    }
}

