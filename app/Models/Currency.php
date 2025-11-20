<?php

namespace App\Models;

/**
 * Modèle Currency - Configuration des billets et pièces
 */
class Currency
{
    private static array $config = [
        'bill_500' => [
            'value' => 500,
            'centimes' => 50000,
            'label' => 'Billet de 500€',
            'img' => "https://argus2euros.fr/wp-content/uploads/2025/04/Billet-de-500-euros-v1-avant.jpg"
        ],
        'bill_200' => [
            'value' => 200,
            'centimes' => 20000,
            'label' => 'Billet de 200€',
            'img' => "https://argus2euros.fr/wp-content/uploads/2025/04/Billet-de-200-euros-v1-avant.jpg"
        ],
        'bill_100' => [
            'value' => 100,
            'centimes' => 10000,
            'label' => 'Billet de 100€',
            'img' => "https://argus2euros.fr/wp-content/uploads/2025/04/Billet-de-100-euros-v1-avant.jpg"
        ],
        'bill_50' => [
            'value' => 50,
            'centimes' => 5000,
            'label' => 'Billet de 50€',
            'img' => "https://argus2euros.fr/wp-content/uploads/2025/04/Billet-de-50-euros-v1-avant.jpg"
        ],
        'bill_20' => [
            'value' => 20,
            'centimes' => 2000,
            'label' => 'Billet de 20€',
            'img' => "https://argus2euros.fr/wp-content/uploads/2025/04/Billet-de-20-euros-v1-avant.jpg"
        ],
        'bill_10' => [
            'value' => 10,
            'centimes' => 1000,
            'label' => 'Billet de 10€',
            'img' => "https://argus2euros.fr/wp-content/uploads/2025/04/Billet-de-10-euros-v1-avant.jpg"
        ],
        'bill_5' => [
            'value' => 5,
            'centimes' => 500,
            'label' => 'Billet de 5€',
            'img' => "https://argus2euros.fr/wp-content/uploads/2025/04/Billet-de-5-euros-v1-avant.jpg"
        ],
        'coin_2' => [
            'value' => 2,
            'centimes' => 200,
            'label' => 'Pièce de 2€',
            'img' => "https://www.ecb.europa.eu/euro/coins/common/shared/img/common_2euro_800.jpg"
        ],
        'coin_1' => [
            'value' => 1,
            'centimes' => 100,
            'label' => 'Pièce de 1€',
            'img' => "https://www.ecb.europa.eu/euro/coins/common/shared/img/common_1euro_800.jpg"
        ],
        'coin_050' => [
            'value' => 0.5,
            'centimes' => 50,
            'label' => 'Pièce de 0,50€',
            'img' => "https://www.ecb.europa.eu/euro/coins/common/shared/img/common_50cent_800.jpg"
        ],
        'coin_020' => [
            'value' => 0.2,
            'centimes' => 20,
            'label' => 'Pièce de 0,20€',
            'img' => "https://www.ecb.europa.eu/euro/coins/common/shared/img/common_20cent_800.jpg"
        ],
        'coin_010' => [
            'value' => 0.1,
            'centimes' => 10,
            'label' => 'Pièce de 0,10€',
            'img' => "https://www.ecb.europa.eu/euro/coins/common/shared/img/common_10cent.gif"
        ],
        'coin_005' => [
            'value' => 0.05,
            'centimes' => 5,
            'label' => 'Pièce de 0,05€',
            'img' => "https://www.ecb.europa.eu/euro/coins/common/shared/img/common_5cent_800.jpg"
        ],
        'coin_002' => [
            'value' => 0.02,
            'centimes' => 2,
            'label' => 'Pièce de 0,02€',
            'img' => "https://www.ecb.europa.eu/euro/coins/common/shared/img/common_2cent_800.jpg"
        ],
        'coin_001' => [
            'value' => 0.01,
            'centimes' => 1,
            'label' => 'Pièce de 0,01€',
            'img' => "https://www.ecb.europa.eu/euro/coins/common/shared/img/common_1cent_800.jpg"
        ]
    ];
    
    /**
     * Obtenir la configuration complète des monnaies
     * @return array Configuration des billets et pièces
     */
    public static function getConfig(): array
    {
        return self::$config;
    }
    
    /**
     * Obtenir les valeurs en centimes
     * @return array Tableau [clé => centimes]
     */
    public static function getValues(): array
    {
        $values = [];
        foreach (self::$config as $key => $config) {
            $values[$key] = $config['centimes'];
        }
        return $values;
    }
    
    /**
     * Obtenir les labels
     * @return array Tableau [clé => label]
     */
    public static function getLabels(): array
    {
        $labels = [];
        foreach (self::$config as $key => $config) {
            $labels[$key] = $config['label'];
        }
        return $labels;
    }
    
    /**
     * Obtenir une configuration spécifique
     * @param string $key Clé de la monnaie
     * @return array|null Configuration ou null si non trouvée
     */
    public static function get(string $key): ?array
    {
        return self::$config[$key] ?? null;
    }
}

