<?php

namespace App\Services;

use App\Entities\Invoice;

/**
 * Classe BaseInvoiceSender - Implémentation de base pour l'envoi de factures
 * Cette classe sert de composant concret dans le pattern Decorator
 */
class BaseInvoiceSender implements InvoiceSender
{
    protected array $logs = [];
    protected string $status = 'pending';
    
    /**
     * Envoyer une facture (implémentation de base - ne fait rien)
     * @param Invoice $invoice Facture à envoyer
     * @return bool True
     */
    public function send(Invoice $invoice): bool
    {
        $this->logs[] = sprintf(
            "[%s] Facture %s créée pour le montant de %s€",
            date('Y-m-d H:i:s'),
            $invoice->getInvoiceNumber(),
            number_format($invoice->getAmountReturned(), 2, ',', ' ')
        );
        
        $this->status = 'created';
        return true;
    }
    
    /**
     * Obtenir le statut de l'envoi
     * @return string Statut de l'envoi
     */
    public function getStatus(): string
    {
        return $this->status;
    }
    
    /**
     * Obtenir les logs d'envoi
     * @return array Logs d'envoi
     */
    public function getLogs(): array
    {
        return $this->logs;
    }
}

