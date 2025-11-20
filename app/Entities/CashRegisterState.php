<?php

namespace App\Entities;

use App\Models\Currency;

/**
 * Classe CashRegisterState - Représente l'état de la caisse
 * Objet immutable créé via le Builder
 */
class CashRegisterState
{
    private array $bills;
    private array $coins;
    private float $totalAmount;
    
    /**
     * Constructeur
     * @param array $bills État des billets
     * @param array $coins État des pièces
     */
    public function __construct(array $bills, array $coins)
    {
        $this->bills = $bills;
        $this->coins = $coins;
        $this->totalAmount = $this->calculateTotal();
    }
    
    /**
     * Obtenir les billets
     * @return array
     */
    public function getBills(): array
    {
        return $this->bills;
    }
    
    /**
     * Obtenir les pièces
     * @return array
     */
    public function getCoins(): array
    {
        return $this->coins;
    }
    
    /**
     * Obtenir tous les éléments (billets + pièces)
     * @return array
     */
    public function getAll(): array
    {
        return array_merge($this->bills, $this->coins);
    }
    
    /**
     * Obtenir le montant total en euros
     * @return float
     */
    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }
    
    /**
     * Calculer le montant total de la caisse
     * @return float
     */
    private function calculateTotal(): float
    {
        $total = 0;
        $currencyValues = Currency::getValues();
        
        foreach ($this->getAll() as $key => $count) {
            if (isset($currencyValues[$key])) {
                $total += ($currencyValues[$key] * $count);
            }
        }
        
        return $total / 100; // Conversion centimes -> euros
    }
    
    /**
     * Convertir en tableau pour la base de données
     * @return array
     */
    public function toArray(): array
    {
        return $this->getAll();
    }
    
    /**
     * Créer un état depuis un tableau de la base de données
     * @param array $data Données de la base
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $bills = [
            'bill_500' => $data['bill_500'] ?? 0,
            'bill_200' => $data['bill_200'] ?? 0,
            'bill_100' => $data['bill_100'] ?? 0,
            'bill_50' => $data['bill_50'] ?? 0,
            'bill_20' => $data['bill_20'] ?? 0,
            'bill_10' => $data['bill_10'] ?? 0,
            'bill_5' => $data['bill_5'] ?? 0,
        ];
        
        $coins = [
            'coin_2' => $data['coin_2'] ?? 0,
            'coin_1' => $data['coin_1'] ?? 0,
            'coin_050' => $data['coin_050'] ?? 0,
            'coin_020' => $data['coin_020'] ?? 0,
            'coin_010' => $data['coin_010'] ?? 0,
            'coin_005' => $data['coin_005'] ?? 0,
            'coin_002' => $data['coin_002'] ?? 0,
            'coin_001' => $data['coin_001'] ?? 0,
        ];
        
        return new self($bills, $coins);
    }
}

