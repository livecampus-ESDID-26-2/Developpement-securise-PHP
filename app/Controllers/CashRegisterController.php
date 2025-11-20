<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CashRegister;
use App\Models\Currency;
use App\Models\Transaction;
use App\Builders\CashRegisterBuilder;
use App\Entities\CashRegisterState;

/**
 * Contrôleur de gestion de la caisse
 */
class CashRegisterController extends Controller
{
    private CashRegister $cashRegisterModel;
    private Transaction $transactionModel;
    
    public function __construct()
    {
        $this->cashRegisterModel = new CashRegister();
        $this->transactionModel = new Transaction();
    }
    
    /**
     * Afficher le formulaire de caisse
     * @return void
     */
    public function index(): void
    {
        $this->requireLogin();
        
        $user = $this->getCurrentUser();
        
        // Récupération de l'état de la caisse
        $cash_register_from_db = $this->cashRegisterModel->getCurrentState();
        
        // Configuration des monnaies
        $currency_config = Currency::getConfig();
        
        // Récupération des dernières transactions
        $recent_transactions = $this->transactionModel->getHistory(5, 0, $user['id']);
        $total_transactions = $this->transactionModel->count($user['id']);
        
        // Construction de l'état de la caisse avec le pattern Builder
        if ($cash_register_from_db) {
            // Créer l'état depuis les données de la base
            $cashRegisterState = CashRegisterState::fromArray($cash_register_from_db);
        } else {
            // Utiliser les valeurs par défaut via le Builder
            $cashRegisterState = CashRegisterBuilder::withDefaults()->build();
        }
        
        // Préparation des valeurs pour la vue
        $initial_bill_values = $cashRegisterState->getBills();
        $initial_coin_values = $cashRegisterState->getCoins();
        
        $this->view('cash_register_form', [
            'user' => $user,
            'currency_config' => $currency_config,
            'initial_bill_values' => $initial_bill_values,
            'initial_coin_values' => $initial_coin_values,
            'recent_transactions' => $recent_transactions,
            'total_transactions' => $total_transactions
        ]);
    }
    
    /**
     * Traiter une transaction
     * @return void
     */
    public function process(): void
    {
        $this->requireLogin();
        
        // Vérifier la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cash-register');
            return;
        }
        
        $user = $this->getCurrentUser();
        
        // Récupération des données
        $amount_due = floatval($_POST['amount_due'] ?? 0);
        $amount_given = floatval($_POST['amount_given'] ?? 0);
        $preferred_value = $_POST['preferred_value'] ?? '';
        $algorithm = $_POST['algorithm'] ?? 'greedy';
        
        // Configuration des monnaies
        $currency_config = Currency::getConfig();
        $currency_values = Currency::getValues();
        $currency_labels = Currency::getLabels();
        
        // Récupération de l'état de caisse actuel
        $current_register = [];
        foreach ($currency_values as $key => $value) {
            $current_register[$key] = intval($_POST[$key] ?? 0);
        }
        
        // Calcul de la monnaie à rendre
        $result = $this->cashRegisterModel->calculateChange(
            $amount_due,
            $amount_given,
            $current_register,
            $algorithm,
            $preferred_value
        );
        
        $change_to_return = $result['change_to_return'];
        $new_register = $result['new_register'];
        $errors = $result['errors'];
        $impossible = $result['impossible'];
        $amount_to_return_cents = round(($amount_given - $amount_due) * 100);
        
        // Si pas d'erreurs, sauvegarder
        if (!$impossible && empty($errors)) {
            // Sauvegarde de l'état de la caisse
            $this->cashRegisterModel->saveState($new_register, $user['id']);
            
            // Enregistrement de la transaction
            $transaction_data = [
                'amount_due' => $amount_due,
                'amount_given' => $amount_given,
                'amount_returned' => $result['amount_returned'],
                'algorithm' => $algorithm,
                'preferred_value' => $preferred_value,
                'change_returned' => $change_to_return,
                'register_before' => $current_register,
                'register_after' => $new_register,
                'user_id' => $user['id']
            ];
            $this->transactionModel->save($transaction_data);
        }
        
        // Affichage du résultat
        $this->view('cash_register_result', [
            'user' => $user,
            'currency_config' => $currency_config,
            'currency_values' => $currency_values,
            'currency_labels' => $currency_labels,
            'amount_due' => $amount_due,
            'amount_given' => $amount_given,
            'amount_to_return_cents' => $amount_to_return_cents,
            'algorithm' => $algorithm,
            'preferred_value' => $preferred_value,
            'current_register' => $current_register,
            'change_to_return' => $change_to_return,
            'new_register' => $new_register,
            'errors' => $errors,
            'impossible' => $impossible
        ]);
    }
    
    /**
     * Afficher l'historique des transactions
     * @return void
     */
    public function history(): void
    {
        $this->requireLogin();
        
        $user = $this->getCurrentUser();
        
        // Récupération de l'historique complet
        $transactions = $this->transactionModel->getHistory(0, 0, $user['id']);
        $total_transactions = $this->transactionModel->count($user['id']);
        
        // Statistiques
        $stats = $this->transactionModel->getUserStats($user['id']);
        
        // Configuration des monnaies
        $currency_config = Currency::getConfig();
        
        $this->view('history', [
            'user' => $user,
            'transactions' => $transactions,
            'total_transactions' => $total_transactions,
            'stats' => $stats,
            'currency_config' => $currency_config
        ]);
    }
}

