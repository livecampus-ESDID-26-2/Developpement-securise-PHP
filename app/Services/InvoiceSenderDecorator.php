<?php

namespace App\Services;

use App\Entities\Invoice;

/**
 * Classe abstraite InvoiceSenderDecorator - Décorateur de base pour l'envoi de factures
 * Pattern Decorator - Permet d'ajouter dynamiquement des fonctionnalités d'envoi
 */
abstract class InvoiceSenderDecorator implements InvoiceSender
{
    protected InvoiceSender $wrappedSender;
    
    /**
     * Constructeur
     * @param InvoiceSender $sender Service d'envoi à décorer
     */
    public function __construct(InvoiceSender $sender)
    {
        $this->wrappedSender = $sender;
    }
    
    /**
     * Envoyer une facture - Délègue au composant décoré
     * @param Invoice $invoice Facture à envoyer
     * @return bool True si succès, false sinon
     */
    public function send(Invoice $invoice): bool
    {
        return $this->wrappedSender->send($invoice);
    }
    
    /**
     * Obtenir le statut de l'envoi
     * @return string Statut de l'envoi
     */
    public function getStatus(): string
    {
        return $this->wrappedSender->getStatus();
    }
    
    /**
     * Obtenir les logs d'envoi
     * @return array Logs d'envoi
     */
    public function getLogs(): array
    {
        return $this->wrappedSender->getLogs();
    }
}

