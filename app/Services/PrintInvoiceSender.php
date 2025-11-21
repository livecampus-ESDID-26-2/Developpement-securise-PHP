<?php

namespace App\Services;

use App\Entities\Invoice;

/**
 * Décorateur PrintInvoiceSender - Impression de factures
 * Pattern Decorator - Ajoute la fonctionnalité d'impression
 */
class PrintInvoiceSender extends InvoiceSenderDecorator
{
    /**
     * Imprimer une facture
     * @param Invoice $invoice Facture à imprimer
     * @return bool True si succès, false sinon
     */
    public function send(Invoice $invoice): bool
    {
        // Appeler le comportement de base
        $result = parent::send($invoice);
        
        if (!$result) {
            return false;
        }
        
        // Ajouter le comportement d'impression
        return $this->printInvoice($invoice);
    }
    
    /**
     * Imprimer la facture
     * @param Invoice $invoice Facture à imprimer
     * @return bool True si succès, false sinon
     */
    private function printInvoice(Invoice $invoice): bool
    {
        // Simulation de l'impression
        // En production, intégrer avec une imprimante réseau ou un service d'impression
        
        $printed = $this->generatePrintFile($invoice);
        
        if ($printed) {
            $this->wrappedSender->getLogs()[] = sprintf(
                "[%s] 🖨️  Facture %s envoyée à l'imprimante",
                date('Y-m-d H:i:s'),
                $invoice->getInvoiceNumber()
            );
            return true;
        } else {
            $this->wrappedSender->getLogs()[] = sprintf(
                "[%s] ❌ Erreur lors de l'impression",
                date('Y-m-d H:i:s')
            );
            return false;
        }
    }
    
    /**
     * Générer un fichier pour l'impression
     * @param Invoice $invoice Facture
     * @return bool True si succès
     */
    private function generatePrintFile(Invoice $invoice): bool
    {
        // Créer un répertoire pour les impressions
        $printDir = __DIR__ . '/../../storage/prints';
        
        if (!is_dir($printDir)) {
            @mkdir($printDir, 0755, true);
        }
        
        $printFile = $printDir . '/print_' . date('Y-m-d_H-i-s') . '_' . $invoice->getInvoiceNumber() . '.html';
        
        // Utiliser le template d'impression
        $printContent = $invoice->toPrintHtml();
        
        return file_put_contents($printFile, $printContent) !== false;
        
        // En production, envoyer à une imprimante réseau :
        // - Utiliser CUPS sur Linux
        // - Utiliser l'API Windows Print
        // - Ou un service d'impression cloud
    }
}

