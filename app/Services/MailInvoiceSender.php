<?php

namespace App\Services;

use App\Entities\Invoice;

/**
 * Décorateur MailInvoiceSender - Envoi de factures par courrier postal
 * Pattern Decorator - Ajoute la fonctionnalité d'envoi par courrier
 */
class MailInvoiceSender extends InvoiceSenderDecorator
{
    /**
     * Envoyer une facture par courrier postal
     * @param Invoice $invoice Facture à envoyer
     * @return bool True si succès, false sinon
     */
    public function send(Invoice $invoice): bool
    {
        // Appeler le comportement de base
        $result = parent::send($invoice);
        
        if (!$result) {
            return false;
        }
        
        // Ajouter le comportement d'envoi par courrier
        return $this->sendByMail($invoice);
    }
    
    /**
     * Envoyer par courrier postal
     * @param Invoice $invoice Facture à envoyer
     * @return bool True si succès, false sinon
     */
    private function sendByMail(Invoice $invoice): bool
    {
        // Simulation de l'envoi par courrier
        // En production, intégrer avec un service de courrier postal (API La Poste, etc.)
        
        $sent = $this->registerMailShipment($invoice);
        
        if ($sent) {
            $this->wrappedSender->getLogs()[] = sprintf(
                "[%s] 📮 Facture %s enregistrée pour envoi par courrier postal",
                date('Y-m-d H:i:s'),
                $invoice->getInvoiceNumber()
            );
            return true;
        } else {
            $this->wrappedSender->getLogs()[] = sprintf(
                "[%s] ❌ Erreur lors de l'enregistrement pour envoi postal",
                date('Y-m-d H:i:s')
            );
            return false;
        }
    }
    
    /**
     * Enregistrer l'envoi par courrier
     * @param Invoice $invoice Facture
     * @return bool True si succès
     */
    private function registerMailShipment(Invoice $invoice): bool
    {
        // Créer un fichier de log pour le courrier
        $logDir = __DIR__ . '/../../storage/mail';
        
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/mail_' . date('Y-m-d_H-i-s') . '_' . $invoice->getInvoiceNumber() . '.txt';
        
        // Utiliser le template de courrier
        $mailContent = $invoice->toMailText();
        
        return file_put_contents($logFile, $mailContent) !== false;
        
        // En production, intégrer avec une API de courrier postal
    }
}

