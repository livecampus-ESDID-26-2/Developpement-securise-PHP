<?php

namespace App\Models;

use App\Core\Model;
use App\Entities\CashRegisterState;

/**
 * Modèle Caisse - Gestion de l'état de la caisse
 */
class CashRegister extends Model
{
    protected string $table = 'cash_register_state';
    
    /**
     * Récupérer l'état actuel de la caisse
     * @return array|null État de la caisse ou null si erreur
     */
    public function getCurrentState(): ?array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY id DESC LIMIT 1");
        $state = $stmt->fetch();
        
        if ($state) {
            // Retirer les champs non nécessaires
            unset($state['id'], $state['updated_at'], $state['updated_by']);
        }
        
        return $state ?: null;
    }
    
    /**
     * Sauvegarder l'état de la caisse
     * @param array|CashRegisterState $register État de la caisse (tableau ou objet CashRegisterState)
     * @param int|null $userId ID de l'utilisateur
     * @return bool True si succès, false sinon
     */
    public function saveState($register, ?int $userId = null): bool
    {
        try {
            // Si on reçoit un CashRegisterState, le convertir en tableau
            if ($register instanceof CashRegisterState) {
                $register = $register->toArray();
            }
            
            $sql = "INSERT INTO {$this->table} (
                bill_500, bill_200, bill_100, bill_50, bill_20, bill_10, bill_5,
                coin_2, coin_1, coin_050, coin_020, coin_010, coin_005, coin_002, coin_001,
                updated_by
            ) VALUES (
                :bill_500, :bill_200, :bill_100, :bill_50, :bill_20, :bill_10, :bill_5,
                :coin_2, :coin_1, :coin_050, :coin_020, :coin_010, :coin_005, :coin_002, :coin_001,
                :updated_by
            )";
            
            $stmt = $this->db->prepare($sql);
            
            $params = $register;
            $params['updated_by'] = $userId;
            
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la sauvegarde de l'état de la caisse : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Calculer la monnaie à rendre selon l'algorithme choisi
     * @param float $amountDue Montant dû
     * @param float $amountGiven Montant donné
     * @param array $currentRegister État actuel de la caisse
     * @param string $algorithm Type d'algorithme ('greedy' ou 'reverse')
     * @param string|null $preferredValue Valeur préférée optionnelle
     * @return array Résultat du calcul [change_to_return, new_register, errors, impossible]
     */
    public function calculateChange(
        float $amountDue,
        float $amountGiven,
        array $currentRegister,
        string $algorithm = 'greedy',
        ?string $preferredValue = null
    ): array {
        $currency_values = Currency::getValues();
        
        // Calcul du montant à rendre en centimes
        $amount_to_return_cents = round(($amountGiven - $amountDue) * 100);
        
        // Validation
        $errors = [];
        
        if ($amountDue <= 0) {
            $errors[] = "Le montant dû doit être supérieur à 0.";
        }
        
        if ($amountGiven < $amountDue) {
            $errors[] = "Le montant donné (" . number_format($amountGiven, 2, ',', ' ') . "€) est insuffisant pour payer " . number_format($amountDue, 2, ',', ' ') . "€.";
        }
        
        // Initialiser tous les compteurs à 0
        $change_to_return = [];
        foreach ($currency_values as $key => $value) {
            $change_to_return[$key] = 0;
        }
        
        $remaining_amount = $amount_to_return_cents;
        $impossible = false;
        
        // Si une valeur préférée est spécifiée
        if (!empty($preferredValue) && isset($currency_values[$preferredValue])) {
            $preferred_value_cents = $currency_values[$preferredValue];
            
            if ($remaining_amount >= $preferred_value_cents && $currentRegister[$preferredValue] > 0) {
                $max_needed = intval($remaining_amount / $preferred_value_cents);
                $available_count = $currentRegister[$preferredValue];
                $count_to_use = min($max_needed, $available_count);
                
                $change_to_return[$preferredValue] = $count_to_use;
                $remaining_amount -= ($count_to_use * $preferred_value_cents);
            }
        }
        
        // Préparer les valeurs selon l'algorithme
        $ordered_values = $currency_values;
        if ($algorithm === 'reverse') {
            $ordered_values = array_reverse($currency_values, true);
        }
        
        // Algorithme pour le reste
        foreach ($ordered_values as $key => $value) {
            if ($key === $preferredValue) {
                continue;
            }
            
            if ($remaining_amount >= $value && $currentRegister[$key] > 0) {
                $max_needed = intval($remaining_amount / $value);
                $available_count = $currentRegister[$key];
                $count_to_return = min($max_needed, $available_count);
                
                $change_to_return[$key] = $count_to_return;
                $remaining_amount -= ($count_to_return * $value);
            }
        }
        
        // Vérification
        if ($remaining_amount > 0) {
            $errors[] = "Impossible de rendre la monnaie exacte avec l'état actuel de la caisse. Il manque " . number_format($remaining_amount / 100, 2, ',', ' ') . "€.";
            $impossible = true;
        }
        
        // Calcul du nouvel état de caisse
        $new_register = [];
        if (!$impossible && empty($errors)) {
            foreach ($currency_values as $key => $value) {
                $new_register[$key] = $currentRegister[$key] - $change_to_return[$key];
            }
            
            // Ajout du montant reçu
            $amount_received_cents = round($amountGiven * 100);
            $amount_to_add = $amount_received_cents;
            
            foreach ($currency_values as $key => $value) {
                if ($amount_to_add >= $value) {
                    $count = intval($amount_to_add / $value);
                    $new_register[$key] += $count;
                    $amount_to_add -= ($count * $value);
                }
            }
        }
        
        return [
            'change_to_return' => $change_to_return,
            'new_register' => $new_register,
            'errors' => $errors,
            'impossible' => $impossible,
            'amount_returned' => $amount_to_return_cents / 100
        ];
    }
}

