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
            // Fallback vers l'ancienne méthode si le template n'existe pas
            return $this->toHtmlLegacy();
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
            return $this->toTextLegacy();
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
     * Méthode legacy pour générer le HTML (fallback)
     * @deprecated Utiliser toHtml() avec templates
     */
    private function toHtmlLegacy(): string
    {
        $html = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture ' . htmlspecialchars($this->invoiceNumber) . '</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .invoice-info { margin-bottom: 30px; }
        .invoice-info table { width: 100%; }
        .invoice-info td { padding: 5px; }
        .details { background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .change-detail { margin-top: 20px; }
        .change-detail table { width: 100%; border-collapse: collapse; }
        .change-detail th, .change-detail td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        .change-detail th { background-color: #333; color: white; }
        .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FACTURE DE RENDU DE MONNAIE</h1>
        <h2>' . htmlspecialchars($this->invoiceNumber) . '</h2>
    </div>
    
    <div class="invoice-info">
        <table>
            <tr>
                <td><strong>Date :</strong></td>
                <td>' . $this->invoiceDate->format('d/m/Y H:i:s') . '</td>
            </tr>
            <tr>
                <td><strong>Client :</strong></td>
                <td>' . ($this->userEmail ? htmlspecialchars($this->userEmail) : 'N/A') . '</td>
            </tr>
        </table>
    </div>
    
    <div class="details">
        <h3>Détails de la transaction</h3>
        <table>
            <tr>
                <td><strong>Montant dû :</strong></td>
                <td>' . number_format($this->amountDue, 2, ',', ' ') . ' €</td>
            </tr>
            <tr>
                <td><strong>Montant donné :</strong></td>
                <td>' . number_format($this->amountGiven, 2, ',', ' ') . ' €</td>
            </tr>
            <tr style="font-size: 1.2em; font-weight: bold;">
                <td>Monnaie rendue :</td>
                <td>' . number_format($this->amountReturned, 2, ',', ' ') . ' €</td>
            </tr>
        </table>
    </div>
    
    <div class="change-detail">
        <h3>Détail du rendu de monnaie</h3>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Valeur</th>
                    <th>Quantité</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($this->changeReturned as $key => $quantity) {
            if ($quantity > 0) {
                $value = $this->getCurrencyValue($key);
                $label = $this->getCurrencyLabel($key);
                $total = $value * $quantity / 100;
                
                $html .= '<tr>
                    <td>' . htmlspecialchars($label) . '</td>
                    <td>' . number_format($value / 100, 2, ',', ' ') . ' €</td>
                    <td>' . $quantity . '</td>
                    <td>' . number_format($total, 2, ',', ' ') . ' €</td>
                </tr>';
            }
        }
        
        $html .= '</tbody>
        </table>
    </div>
    
    <div class="footer">
        <p>Merci de votre confiance</p>
        <p><em>Document généré automatiquement - ' . $this->invoiceDate->format('d/m/Y à H:i:s') . '</em></p>
    </div>
</body>
</html>';
        
        return $html;
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
    
    /**
     * Méthode legacy pour générer du texte (fallback)
     * @deprecated Utiliser toMailText() avec templates
     */
    private function toTextLegacy(): string
    {
        $text = "FACTURE " . $this->invoiceNumber . "\n";
        $text .= "Date: " . $this->invoiceDate->format('d/m/Y H:i:s') . "\n";
        $text .= "Montant rendu: " . number_format($this->amountReturned, 2, ',', ' ') . " €\n";
        return $text;
    }
}

