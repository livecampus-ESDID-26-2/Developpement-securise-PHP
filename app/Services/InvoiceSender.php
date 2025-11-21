<?php

namespace App\Services;

use App\Entities\Invoice;

/**
 * Interface InvoiceSender - Contrat pour l'envoi de factures
 * Pattern Decorator pour l'envoi de factures par différents canaux
 */
interface InvoiceSender
{
    /**
     * Envoyer une facture
     * @param Invoice $invoice Facture à envoyer
     * @return bool True si succès, false sinon
     */
    public function send(Invoice $invoice): bool;
    
    /**
     * Obtenir le statut de l'envoi
     * @return string Statut de l'envoi
     */
    public function getStatus(): string;
    
    /**
     * Obtenir les logs d'envoi
     * @return array Logs d'envoi
     */
    public function getLogs(): array;
}

