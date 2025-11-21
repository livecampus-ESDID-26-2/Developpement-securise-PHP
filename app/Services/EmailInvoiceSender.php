<?php

namespace App\Services;

use App\Entities\Invoice;

/**
 * Décorateur EmailInvoiceSender - Envoi de factures par email
 * Pattern Decorator - Ajoute la fonctionnalité d'envoi par email
 */
class EmailInvoiceSender extends InvoiceSenderDecorator
{
    /**
     * Envoyer une facture par email
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
        
        // Ajouter le comportement d'envoi par email
        return $this->sendEmail($invoice);
    }
    
    /**
     * Envoyer l'email
     * @param Invoice $invoice Facture à envoyer
     * @return bool True si succès, false sinon
     */
    private function sendEmail(Invoice $invoice): bool
    {
        $userEmail = $invoice->getUserEmail();
        
        if (empty($userEmail)) {
            $this->wrappedSender->getLogs()[] = sprintf(
                "[%s] ❌ Envoi email impossible : email utilisateur manquant",
                date('Y-m-d H:i:s')
            );
            return false;
        }
        
        // Simulation de l'envoi d'email
        // En production, utiliser une bibliothèque comme PHPMailer ou Symfony Mailer
        $subject = "Facture n° " . $invoice->getInvoiceNumber();
        $message = $this->buildEmailMessage($invoice);
        $headers = $this->buildEmailHeaders();
        
        // Simulation (en production, utiliser mail() ou un service SMTP)
        $sent = $this->simulateEmailSending($userEmail, $subject, $message, $headers);
        
        if ($sent) {
            $this->wrappedSender->getLogs()[] = sprintf(
                "[%s] ✉️  Facture %s envoyée par email à %s",
                date('Y-m-d H:i:s'),
                $invoice->getInvoiceNumber(),
                $userEmail
            );
            return true;
        } else {
            $this->wrappedSender->getLogs()[] = sprintf(
                "[%s] ❌ Erreur lors de l'envoi email à %s",
                date('Y-m-d H:i:s'),
                $userEmail
            );
            return false;
        }
    }
    
    /**
     * Construire le message de l'email
     * @param Invoice $invoice Facture
     * @return string Message HTML
     */
    private function buildEmailMessage(Invoice $invoice): string
    {
        return $invoice->toHtml();
    }
    
    /**
     * Construire les en-têtes de l'email
     * @return string En-têtes
     */
    private function buildEmailHeaders(): string
    {
        return implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            'From: noreply@cash-register.com',
            'Reply-To: support@cash-register.com',
            'X-Mailer: PHP/' . phpversion()
        ]);
    }
    
    /**
     * Simuler l'envoi d'un email
     * @param string $to Destinataire
     * @param string $subject Sujet
     * @param string $message Message
     * @param string $headers En-têtes
     * @return bool True si succès
     */
    private function simulateEmailSending(string $to, string $subject, string $message, string $headers): bool
    {
        // En développement, on enregistre l'email dans un fichier log
        $logDir = __DIR__ . '/../../storage/emails';
        
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/email_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.html';
        
        $emailContent = "<!-- Email simulé -->\n";
        $emailContent .= "<!-- To: {$to} -->\n";
        $emailContent .= "<!-- Subject: {$subject} -->\n";
        $emailContent .= "<!-- Headers: {$headers} -->\n\n";
        $emailContent .= $message;
        
        return file_put_contents($logFile, $emailContent) !== false;
        
        // En production, utiliser :
        // return mail($to, $subject, $message, $headers);
    }
}

