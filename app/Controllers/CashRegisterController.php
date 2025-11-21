<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CashRegister;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\Invoice as InvoiceModel;
use App\Builders\CashRegisterBuilder;
use App\Entities\CashRegisterState;
use App\Entities\Invoice as InvoiceEntity;
use App\Services\BaseInvoiceSender;
use App\Services\EmailInvoiceSender;
use App\Services\MailInvoiceSender;
use App\Services\PrintInvoiceSender;

/**
 * Contrôleur de gestion de la caisse
 */
class CashRegisterController extends Controller
{
    private CashRegister $cashRegisterModel;
    private Transaction $transactionModel;
    private InvoiceModel $invoiceModel;
    
    public function __construct()
    {
        $this->cashRegisterModel = new CashRegister();
        $this->transactionModel = new Transaction();
        $this->invoiceModel = new InvoiceModel();
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
        $invoiceId = null;
        $invoiceNumber = null;
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
            $transactionId = $this->transactionModel->save($transaction_data);
            
            // Génération de la facture
            if ($transactionId) {
                $invoiceNumber = InvoiceEntity::generateInvoiceNumber();
                $invoice_data = [
                    'transaction_id' => $transactionId,
                    'invoice_number' => $invoiceNumber,
                    'amount_due' => $amount_due,
                    'amount_given' => $amount_given,
                    'amount_returned' => $result['amount_returned'],
                    'change_returned' => $change_to_return,
                    'user_id' => $user['id'],
                    'status' => 'pending'
                ];
                $invoiceId = $this->invoiceModel->create($invoice_data);
            }
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
            'impossible' => $impossible,
            'invoice_id' => $invoiceId,
            'invoice_number' => $invoiceNumber
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
        
        // Récupération des factures associées à chaque transaction
        $invoices = [];
        foreach ($transactions as $transaction) {
            $invoice = $this->invoiceModel->findByTransactionId($transaction['id']);
            if ($invoice) {
                $invoices[$transaction['id']] = $invoice;
            }
        }
        
        // Statistiques
        $stats = $this->transactionModel->getUserStats($user['id']);
        
        // Configuration des monnaies
        $currency_config = Currency::getConfig();
        
        $this->view('history', [
            'user' => $user,
            'transactions' => $transactions,
            'total_transactions' => $total_transactions,
            'stats' => $stats,
            'currency_config' => $currency_config,
            'invoices' => $invoices
        ]);
    }
    
    /**
     * Afficher une facture
     * @return void
     */
    public function viewInvoice(): void
    {
        $this->requireLogin();
        
        $user = $this->getCurrentUser();
        $invoiceId = intval($_GET['id'] ?? 0);
        
        if (!$invoiceId) {
            $this->redirect('/cash-register');
            return;
        }
        
        // Récupération de la facture
        $invoice = $this->invoiceModel->findById($invoiceId);
        
        if (!$invoice) {
            $this->redirect('/cash-register');
            return;
        }
        
        // Vérifier que l'utilisateur a accès à cette facture
        if ($invoice->getUserId() !== $user['id'] && $user['role'] !== 'admin') {
            $this->redirect('/cash-register');
            return;
        }
        
        // Afficher la facture HTML
        echo $invoice->toHtml();
    }
    
    /**
     * Envoyer une facture
     * @return void
     */
    public function sendInvoice(): void
    {
        $this->requireLogin();
        
        $user = $this->getCurrentUser();
        
        // Vérifier la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        $invoiceId = intval($_POST['invoice_id'] ?? 0);
        $sendMethod = $_POST['send_method'] ?? '';
        
        if (!$invoiceId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID de facture manquant']);
            return;
        }
        
        // Récupération de la facture
        $invoice = $this->invoiceModel->findById($invoiceId);
        
        if (!$invoice) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Facture non trouvée']);
            return;
        }
        
        // Vérifier que l'utilisateur a accès à cette facture
        if ($invoice->getUserId() !== $user['id'] && $user['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
            return;
        }
        
        // Construction du sender avec le pattern Decorator
        $sender = $this->buildInvoiceSender($sendMethod);
        
        // Envoi de la facture
        $success = $sender->send($invoice);
        
        // Mise à jour du statut si succès
        if ($success) {
            $newStatus = $this->getInvoiceStatus($sendMethod);
            $this->invoiceModel->updateStatus($invoiceId, $newStatus);
        }
        
        // Retour JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Facture envoyée avec succès' : 'Erreur lors de l\'envoi',
            'logs' => $sender->getLogs(),
            'status' => $sender->getStatus()
        ]);
    }
    
    /**
     * Construire le sender selon la méthode d'envoi (Pattern Decorator)
     * @param string $method Méthode d'envoi (email, mail, print, all)
     * @return \App\Services\InvoiceSender Sender configuré
     */
    private function buildInvoiceSender(string $method): \App\Services\InvoiceSender
    {
        // Créer le composant de base
        $sender = new BaseInvoiceSender();
        
        // Appliquer les décorateurs selon la méthode
        switch ($method) {
            case 'email':
                $sender = new EmailInvoiceSender($sender);
                break;
            
            case 'mail':
                $sender = new MailInvoiceSender($sender);
                break;
            
            case 'print':
                $sender = new PrintInvoiceSender($sender);
                break;
            
            case 'all':
                // Exemple de composition de plusieurs décorateurs
                $sender = new EmailInvoiceSender($sender);
                $sender = new PrintInvoiceSender($sender);
                $sender = new MailInvoiceSender($sender);
                break;
            
            default:
                // Par défaut, juste le composant de base
                break;
        }
        
        return $sender;
    }
    
    /**
     * Obtenir le statut de facture selon la méthode d'envoi
     * @param string $method Méthode d'envoi
     * @return string Statut
     */
    private function getInvoiceStatus(string $method): string
    {
        switch ($method) {
            case 'email':
                return 'sent_email';
            case 'mail':
                return 'sent_mail';
            case 'print':
                return 'printed';
            case 'all':
                return 'sent_email'; // Considéré comme envoyé par email principalement
            default:
                return 'pending';
        }
    }
}

