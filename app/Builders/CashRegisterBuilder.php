<?php

namespace App\Builders;

use App\Entities\CashRegisterState;

/**
 * Classe CashRegisterBuilder - Pattern Builder pour construire l'état de la caisse
 * Permet de construire de manière fluide et flexible l'état initial d'une caisse
 */
class CashRegisterBuilder
{
    private array $bills = [];
    private array $coins = [];
    
    /**
     * Créer un nouveau builder
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }
    
    /**
     * Créer un builder avec les valeurs par défaut
     * @return self
     */
    public static function withDefaults(): self
    {
        return (new self())
            ->setBill500(1)
            ->setBill200(2)
            ->setBill100(2)
            ->setBill50(4)
            ->setBill20(1)
            ->setBill10(23)
            ->setBill5(0)
            ->setCoin2(34)
            ->setCoin1(23)
            ->setCoin050(23)
            ->setCoin020(80)
            ->setCoin010(12)
            ->setCoin005(8)
            ->setCoin002(45)
            ->setCoin001(12);
    }
    
    /**
     * Créer un builder avec une caisse vide
     * @return self
     */
    public static function empty(): self
    {
        return (new self())
            ->setBill500(0)
            ->setBill200(0)
            ->setBill100(0)
            ->setBill50(0)
            ->setBill20(0)
            ->setBill10(0)
            ->setBill5(0)
            ->setCoin2(0)
            ->setCoin1(0)
            ->setCoin050(0)
            ->setCoin020(0)
            ->setCoin010(0)
            ->setCoin005(0)
            ->setCoin002(0)
            ->setCoin001(0);
    }
    
    /**
     * Créer un builder depuis un état existant
     * @param CashRegisterState $state
     * @return self
     */
    public static function fromState(CashRegisterState $state): self
    {
        $builder = new self();
        $all = $state->getAll();
        
        foreach ($all as $key => $value) {
            if (strpos($key, 'bill_') === 0) {
                $builder->bills[$key] = $value;
            } else {
                $builder->coins[$key] = $value;
            }
        }
        
        return $builder;
    }
    
    // ========== Méthodes pour les billets ==========
    
    /**
     * Définir le nombre de billets de 500€
     * @param int $count Nombre de billets
     * @return self
     */
    public function setBill500(int $count): self
    {
        $this->bills['bill_500'] = max(0, $count);
        return $this;
    }
    
    /**
     * Définir le nombre de billets de 200€
     * @param int $count Nombre de billets
     * @return self
     */
    public function setBill200(int $count): self
    {
        $this->bills['bill_200'] = max(0, $count);
        return $this;
    }
    
    /**
     * Définir le nombre de billets de 100€
     * @param int $count Nombre de billets
     * @return self
     */
    public function setBill100(int $count): self
    {
        $this->bills['bill_100'] = max(0, $count);
        return $this;
    }
    
    /**
     * Définir le nombre de billets de 50€
     * @param int $count Nombre de billets
     * @return self
     */
    public function setBill50(int $count): self
    {
        $this->bills['bill_50'] = max(0, $count);
        return $this;
    }
    
    /**
     * Définir le nombre de billets de 20€
     * @param int $count Nombre de billets
     * @return self
     */
    public function setBill20(int $count): self
    {
        $this->bills['bill_20'] = max(0, $count);
        return $this;
    }
    
    /**
     * Définir le nombre de billets de 10€
     * @param int $count Nombre de billets
     * @return self
     */
    public function setBill10(int $count): self
    {
        $this->bills['bill_10'] = max(0, $count);
        return $this;
    }
    
    /**
     * Définir le nombre de billets de 5€
     * @param int $count Nombre de billets
     * @return self
     */
    public function setBill5(int $count): self
    {
        $this->bills['bill_5'] = max(0, $count);
        return $this;
    }
    
    // ========== Méthodes pour les pièces ==========
    
    /**
     * Définir le nombre de pièces de 2€
     * @param int $count Nombre de pièces
     * @return self
     */
    public function setCoin2(int $count): self
    {
        $this->coins['coin_2'] = max(0, $count);
        return $this;
    }
    
    /**
     * Définir le nombre de pièces de 1€
     * @param int $count Nombre de pièces
     * @return self
     */
    public function setCoin1(int $count): self
    {
        $this->coins['coin_1'] = max(0, $count);
        return $this;
    }
    
    /**
     * Définir le nombre de pièces de 0,50€
     * @param int $count Nombre de pièces
     * @return self
     */
    public function setCoin050(int $count): self
    {
        $this->coins['coin_050'] = max(0, $count);
        return $this;
    }
    
    /**
     * Définir le nombre de pièces de 0,20€
     * @param int $count Nombre de pièces
     * @return self
     */
    public function setCoin020(int $count): self
    {
        $this->coins['coin_020'] = max(0, $count);
        return $this;
    }
    
    /**
     * Définir le nombre de pièces de 0,10€
     * @param int $count Nombre de pièces
     * @return self
     */
    public function setCoin010(int $count): self
    {
        $this->coins['coin_010'] = max(0, $count);
        return $this;
    }
    
    /**
     * Définir le nombre de pièces de 0,05€
     * @param int $count Nombre de pièces
     * @return self
     */
    public function setCoin005(int $count): self
    {
        $this->coins['coin_005'] = max(0, $count);
        return $this;
    }
    
    /**
     * Définir le nombre de pièces de 0,02€
     * @param int $count Nombre de pièces
     * @return self
     */
    public function setCoin002(int $count): self
    {
        $this->coins['coin_002'] = max(0, $count);
        return $this;
    }
    
    /**
     * Définir le nombre de pièces de 0,01€
     * @param int $count Nombre de pièces
     * @return self
     */
    public function setCoin001(int $count): self
    {
        $this->coins['coin_001'] = max(0, $count);
        return $this;
    }
    
    // ========== Méthodes utilitaires ==========
    
    /**
     * Ajouter un certain nombre de billets/pièces
     * @param string $key Clé (ex: 'bill_500', 'coin_2')
     * @param int $count Nombre à ajouter
     * @return self
     */
    public function add(string $key, int $count): self
    {
        if (strpos($key, 'bill_') === 0) {
            $this->bills[$key] = ($this->bills[$key] ?? 0) + $count;
        } elseif (strpos($key, 'coin_') === 0) {
            $this->coins[$key] = ($this->coins[$key] ?? 0) + $count;
        }
        
        return $this;
    }
    
    /**
     * Retirer un certain nombre de billets/pièces
     * @param string $key Clé (ex: 'bill_500', 'coin_2')
     * @param int $count Nombre à retirer
     * @return self
     */
    public function remove(string $key, int $count): self
    {
        if (strpos($key, 'bill_') === 0) {
            $this->bills[$key] = max(0, ($this->bills[$key] ?? 0) - $count);
        } elseif (strpos($key, 'coin_') === 0) {
            $this->coins[$key] = max(0, ($this->coins[$key] ?? 0) - $count);
        }
        
        return $this;
    }
    
    /**
     * Réinitialiser tous les billets à 0
     * @return self
     */
    public function resetBills(): self
    {
        $this->bills = [
            'bill_500' => 0,
            'bill_200' => 0,
            'bill_100' => 0,
            'bill_50' => 0,
            'bill_20' => 0,
            'bill_10' => 0,
            'bill_5' => 0,
        ];
        return $this;
    }
    
    /**
     * Réinitialiser toutes les pièces à 0
     * @return self
     */
    public function resetCoins(): self
    {
        $this->coins = [
            'coin_2' => 0,
            'coin_1' => 0,
            'coin_050' => 0,
            'coin_020' => 0,
            'coin_010' => 0,
            'coin_005' => 0,
            'coin_002' => 0,
            'coin_001' => 0,
        ];
        return $this;
    }
    
    /**
     * Construire l'objet CashRegisterState final
     * @return CashRegisterState
     */
    public function build(): CashRegisterState
    {
        // S'assurer que tous les champs sont initialisés
        $bills = array_merge([
            'bill_500' => 0,
            'bill_200' => 0,
            'bill_100' => 0,
            'bill_50' => 0,
            'bill_20' => 0,
            'bill_10' => 0,
            'bill_5' => 0,
        ], $this->bills);
        
        $coins = array_merge([
            'coin_2' => 0,
            'coin_1' => 0,
            'coin_050' => 0,
            'coin_020' => 0,
            'coin_010' => 0,
            'coin_005' => 0,
            'coin_002' => 0,
            'coin_001' => 0,
        ], $this->coins);
        
        return new CashRegisterState($bills, $coins);
    }
}

