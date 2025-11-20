<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Currency;

/**
 * Contrôleur d'administration
 */
class AdminController extends Controller
{
    private User $userModel;
    private Transaction $transactionModel;
    
    public function __construct()
    {
        $this->userModel = new User();
        $this->transactionModel = new Transaction();
    }
    
    /**
     * Dashboard administrateur
     * @return void
     */
    public function dashboard(): void
    {
        $this->requireAdmin();
        
        $user = $this->getCurrentUser();
        
        // Récupération de tous les utilisateurs (sauf admins)
        $users = $this->userModel->getAllUsers();
        
        // Statistiques globales
        $stats = $this->transactionModel->getGlobalStats();
        
        // Pour chaque utilisateur, récupérer ses statistiques et dernières transactions
        $users_data = [];
        foreach ($users as $current_user) {
            $user_stats = $this->transactionModel->getUserStats($current_user['id']);
            $last_transactions = $this->transactionModel->getHistory(3, 0, $current_user['id']);
            
            $users_data[] = [
                'user' => $current_user,
                'stats' => $user_stats,
                'last_transactions' => $last_transactions
            ];
        }
        
        $this->view('admin/dashboard', [
            'user' => $user,
            'users_data' => $users_data,
            'stats' => $stats
        ]);
    }
    
    /**
     * Historique global de toutes les transactions
     * @return void
     */
    public function history(): void
    {
        $this->requireAdmin();
        
        $user = $this->getCurrentUser();
        
        // Récupération de toutes les transactions
        $transactions = $this->transactionModel->getHistory();
        
        // Configuration des monnaies
        $currency_config = Currency::getConfig();
        
        // Récupération des utilisateurs pour afficher les noms
        $users = $this->userModel->getAllWithRole();
        $users_map = [];
        foreach ($users as $current_user) {
            $users_map[$current_user['id']] = $current_user;
        }
        
        $this->view('admin/history', [
            'user' => $user,
            'transactions' => $transactions,
            'currency_config' => $currency_config,
            'users_map' => $users_map
        ]);
    }
    
    /**
     * Détails d'un utilisateur spécifique
     * @param string $userId ID de l'utilisateur
     * @return void
     */
    public function userDetail(string $userId): void
    {
        $this->requireAdmin();
        
        $user = $this->getCurrentUser();
        $userId = (int) $userId;
        
        // Récupération de l'utilisateur
        $target_user = $this->userModel->find($userId);
        
        if (!$target_user) {
            $this->redirect('/admin/dashboard');
            return;
        }
        
        // Récupération des transactions de l'utilisateur
        $transactions = $this->transactionModel->getHistory(0, 0, $userId);
        
        // Statistiques de l'utilisateur
        $stats = $this->transactionModel->getUserStats($userId);
        
        // Configuration des monnaies
        $currency_config = Currency::getConfig();
        
        $this->view('admin/user_detail', [
            'user' => $user,
            'target_user' => $target_user,
            'transactions' => $transactions,
            'stats' => $stats,
            'currency_config' => $currency_config
        ]);
    }
}

