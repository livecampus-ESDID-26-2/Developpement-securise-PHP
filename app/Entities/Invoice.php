<?php

namespace App\Entities;

use App\Services\TemplateEngine;

/**
 * Classe Invoice - Représente une facture
 * Entité immutable générée après une transaction
 */
class Invoice
{
    private int $id;
    private int $transactionId;
    private string $invoiceNumber;
    private float $amountDue;
    private float $amountGiven;
    private float $amountReturned;
    private array $changeReturned;
    private \DateTime $invoiceDate;
    private int $userId;
    private ?string $userEmail;
    private string $status;
    
    /**
     * Constructeur
     */
    public function __construct(
        int $id,
        int $transactionId,
        string $invoiceNumber,
        float $amountDue,
        float $amountGiven,
        float $amountReturned,
        array $changeReturned,
        \DateTime $invoiceDate,
        int $userId,
        ?string $userEmail = null,
        string $status = 'pending'
    ) {
        $this->id = $id;
        $this->transactionId = $transactionId;
        $this->invoiceNumber = $invoiceNumber;
        $this->amountDue = $amountDue;
        $this->amountGiven = $amountGiven;
        $this->amountReturned = $amountReturned;
        $this->changeReturned = $changeReturned;
        $this->invoiceDate = $invoiceDate;
        $this->userId = $userId;
        $this->userEmail = $userEmail;
        $this->status = $status;
    }
    
    /**
     * Getters
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getTransactionId(): int
    {
        return $this->transactionId;
    }
    
    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }
    
    public function getAmountDue(): float
    {
        return $this->amountDue;
    }
    
    public function getAmountGiven(): float
    {
        return $this->amountGiven;
    }
    
    public function getAmountReturned(): float
    {
        return $this->amountReturned;
    }
    
    public function getChangeReturned(): array
    {
        return $this->changeReturned;
    }
    
    public function getInvoiceDate(): \DateTime
    {
        return $this->invoiceDate;
    }
    
    public function getUserId(): int
    {
        return $this->userId;
    }
    
    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }
    
    /**
     * Générer un numéro de facture unique
     * Format: INV-YYYYMMDD-XXXXXX
     */
    public static function generateInvoiceNumber(): string
    {
        $date = date('Ymd');
        $random = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        return "INV-{$date}-{$random}";
    }
    
    /**
     * Créer une facture depuis un tableau de données
     */
    public static function fromArray(array $data): self
    {
        $changeReturned = is_string($data['change_returned']) 
            ? json_decode($data['change_returned'], true) 
            : $data['change_returned'];
            
        $invoiceDate = $data['invoice_date'] instanceof \DateTime 
            ? $data['invoice_date'] 
            : new \DateTime($data['invoice_date']);
        
        return new self(
            $data['id'],
            $data['transaction_id'],
            $data['invoice_number'],
            (float) $data['amount_due'],
            (float) $data['amount_given'],
            (float) $data['amount_returned'],
            $changeReturned,
            $invoiceDate,
            $data['user_id'],
            $data['user_email'] ?? null,
            $data['status'] ?? 'pending'
        );
    }
    
    /**
     * Convertir en tableau pour l'affichage ou la base de données
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transactionId,
            'invoice_number' => $this->invoiceNumber,
            'amount_due' => $this->amountDue,
            'amount_given' => $this->amountGiven,
            'amount_returned' => $this->amountReturned,
            'change_returned' => $this->changeReturned,
            'invoice_date' => $this->invoiceDate->format('Y-m-d H:i:s'),
            'user_id' => $this->userId,
            'user_email' => $this->userEmail,
            'status' => $this->status
        ];
    }
    
    /**
     * Préparer les variables pour les templates
     * @return array Tableau de variables
     */
    private function prepareTemplateVariables(): array
    {
        $currencyLabels = [
            'bill_500' => 'Billet 500€', 'bill_200' => 'Billet 200€', 'bill_100' => 'Billet 100€',
            'bill_50' => 'Billet 50€', 'bill_20' => 'Billet 20€', 'bill_10' => 'Billet 10€',
            'bill_5' => 'Billet 5€', 'coin_2' => 'Pièce 2€', 'coin_1' => 'Pièce 1€',
            'coin_050' => 'Pièce 0,50€', 'coin_020' => 'Pièce 0,20€', 'coin_010' => 'Pièce 0,10€',
            'coin_005' => 'Pièce 0,05€', 'coin_002' => 'Pièce 0,02€', 'coin_001' => 'Pièce 0,01€'
        ];
        
        // Générer les lignes du tableau de détail (HTML)
        $changeDetailRows = '';
        foreach ($this->changeReturned as $key => $quantity) {
            if ($quantity > 0) {
                $value = $this->getCurrencyValue($key) / 100;
                $label = $currencyLabels[$key] ?? $key;
                $total = $value * $quantity;
                
                $changeDetailRows .= sprintf(
                    '<tr><td>%s</td><td>%s €</td><td>%d</td><td>%s €</td></tr>',
                    htmlspecialchars($label),
                    number_format($value, 2, ',', ' '),
                    $quantity,
                    number_format($total, 2, ',', ' ')
                );
            }
        }
        
        // Générer le détail texte (pour mail/sms)
        $changeDetailText = '';
        foreach ($this->changeReturned as $key => $quantity) {
            if ($quantity > 0) {
                $value = $this->getCurrencyValue($key) / 100;
                $label = $currencyLabels[$key] ?? $key;
                $total = $value * $quantity;
                
                $changeDetailText .= sprintf(
                    "%-20s x %2d  =  %8s €\n",
                    $label,
                    $quantity,
                    number_format($total, 2, ',', ' ')
                );
            }
        }
        
        return [
            'invoice_number' => $this->invoiceNumber,
            'invoice_date' => $this->invoiceDate->format('d/m/Y à H:i:s'),
            'user_email' => $this->userEmail ?? 'N/A',
            'status' => $this->getStatusLabel(),
            'amount_due' => number_format($this->amountDue, 2, ',', ' '),
            'amount_given' => number_format($this->amountGiven, 2, ',', ' '),
            'amount_returned' => number_format($this->amountReturned, 2, ',', ' '),
            'change_detail_rows' => $changeDetailRows,
            'change_detail_text' => $changeDetailText
        ];
    }
    
    /**
     * Obtenir le libellé du statut
     * @return string Libellé du statut
     */
    private function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'En attente',
            'sent_email' => 'Envoyée par email',
            'sent_mail' => 'Envoyée par courrier',
            'printed' => 'Imprimée',
            'sent_sms' => 'Envoyée par SMS'
        ];
        
        return $labels[$this->status] ?? $this->status;
    }
    
    /**
     * Générer le contenu HTML de la facture (pour email)
     */
    public function toHtml(): string
    {
        $templatePath = __DIR__ . '/../Templates/email.html';
        
        if (!TemplateEngine::exists($templatePath)) {
            // Fallback simple si le template n'existe pas
            return sprintf(
                '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Facture</title></head>' .
                '<body style="font-family: Arial, sans-serif; padding: 20px;">' .
                '<h1>Facture %s</h1>' .
                '<p>Date: %s</p>' .
                '<p>Client: %s</p>' .
                '<p>Montant rendu: %s €</p>' .
                '</body></html>',
                htmlspecialchars($this->invoiceNumber),
                $this->invoiceDate->format('d/m/Y H:i:s'),
                htmlspecialchars($this->userEmail ?? 'N/A'),
                number_format($this->amountReturned, 2, ',', ' ')
            );
        }
        
        return TemplateEngine::render($templatePath, $this->prepareTemplateVariables());
    }
    
    /**
     * Générer le contenu HTML pour l'impression
     */
    public function toPrintHtml(): string
    {
        $templatePath = __DIR__ . '/../Templates/print.html';
        
        if (!TemplateEngine::exists($templatePath)) {
            return $this->toHtml(); // Fallback vers email template
        }
        
        return TemplateEngine::render($templatePath, $this->prepareTemplateVariables());
    }
    
    /**
     * Générer le contenu texte pour le courrier postal
     */
    public function toMailText(): string
    {
        $templatePath = __DIR__ . '/../Templates/mail.txt';
        
        if (!TemplateEngine::exists($templatePath)) {
            // Fallback texte simple
            return sprintf(
                "FACTURE %s\nDate: %s\nClient: %s\nMontant rendu: %s €\n",
                $this->invoiceNumber,
                $this->invoiceDate->format('d/m/Y H:i:s'),
                $this->userEmail ?? 'N/A',
                number_format($this->amountReturned, 2, ',', ' ')
            );
        }
        
        return TemplateEngine::render($templatePath, $this->prepareTemplateVariables());
    }
    
    /**
     * Générer le fichier SMS complet (message + log)
     * @param string $phoneNumber Numéro de téléphone
     * @return string Contenu du fichier SMS
     */
    public function toSmsText(string $phoneNumber = 'N/A'): string
    {
        $templatePath = __DIR__ . '/../Templates/sms.txt';
        
        $variables = $this->prepareTemplateVariables();
        $variables['date_envoi'] = date('d/m/Y H:i:s');
        $variables['phone_number'] = $phoneNumber;
        
        // Extraire le message court du template (ligne entre "--- MESSAGE ---" et "---------------")
        $messageShort = "Facture {$this->invoiceNumber}: Montant rendu " . 
                       number_format($this->amountReturned, 2, ',', ' ') . "€. " .
                       "Consultez votre facture sur notre site. Merci!";
        
        $variables['message_length'] = strlen($messageShort);
        
        if (!TemplateEngine::exists($templatePath)) {
            // Fallback texte simple
            return "SMS envoyé au {$phoneNumber}\nMessage: {$messageShort}\nFacture: {$this->invoiceNumber}";
        }
        
        return TemplateEngine::render($templatePath, $variables);
    }
    
    /**
     * Obtenir la valeur en centimes d'une devise
     */
    private function getCurrencyValue(string $key): int
    {
        $values = [
            'bill_500' => 50000, 'bill_200' => 20000, 'bill_100' => 10000,
            'bill_50' => 5000, 'bill_20' => 2000, 'bill_10' => 1000, 'bill_5' => 500,
            'coin_2' => 200, 'coin_1' => 100, 'coin_050' => 50, 'coin_020' => 20,
            'coin_010' => 10, 'coin_005' => 5, 'coin_002' => 2, 'coin_001' => 1
        ];
        
        return $values[$key] ?? 0;
    }
    
    /**
     * Obtenir le libellé d'une devise
     */
    private function getCurrencyLabel(string $key): string
    {
        $labels = [
            'bill_500' => 'Billet 500€', 'bill_200' => 'Billet 200€', 'bill_100' => 'Billet 100€',
            'bill_50' => 'Billet 50€', 'bill_20' => 'Billet 20€', 'bill_10' => 'Billet 10€',
            'bill_5' => 'Billet 5€', 'coin_2' => 'Pièce 2€', 'coin_1' => 'Pièce 1€',
            'coin_050' => 'Pièce 0,50€', 'coin_020' => 'Pièce 0,20€', 'coin_010' => 'Pièce 0,10€',
            'coin_005' => 'Pièce 0,05€', 'coin_002' => 'Pièce 0,02€', 'coin_001' => 'Pièce 0,01€'
        ];
        
        return $labels[$key] ?? $key;
    }
}

