<?php

namespace App\Services;

use App\Entities\Invoice;

/**
 * Décorateur SmsInvoiceSender - Envoi de factures par SMS
 * Pattern Decorator - Ajoute la fonctionnalité d'envoi par SMS
 */
class SmsInvoiceSender extends InvoiceSenderDecorator
{
    /**
     * Envoyer une facture par SMS
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
        
        // Ajouter le comportement d'envoi par SMS
        return $this->sendSms($invoice);
    }
    
    /**
     * Envoyer le SMS
     * @param Invoice $invoice Facture à envoyer
     * @return bool True si succès, false sinon
     */
    private function sendSms(Invoice $invoice): bool
    {
        $userEmail = $invoice->getUserEmail();
        
        if (empty($userEmail)) {
            $this->wrappedSender->getLogs()[] = sprintf(
                "[%s] ❌ Envoi SMS impossible : email utilisateur manquant",
                date('Y-m-d H:i:s')
            );
            return false;
        }
        
        // Simulation de l'envoi de SMS
        // En production, utiliser une API comme Twilio, Nexmo, OVH, etc.
        $phoneNumber = $this->extractPhoneFromEmail($userEmail);
        $message = $this->buildSmsMessage($invoice);
        
        // Simulation (en production, utiliser une API SMS)
        $sent = $this->simulateSmsSending($phoneNumber, $message, $invoice);
        
        if ($sent) {
            $this->wrappedSender->getLogs()[] = sprintf(
                "[%s] 📱 Facture %s envoyée par SMS au %s",
                date('Y-m-d H:i:s'),
                $invoice->getInvoiceNumber(),
                $phoneNumber
            );
            return true;
        } else {
            $this->wrappedSender->getLogs()[] = sprintf(
                "[%s] ❌ Erreur lors de l'envoi SMS au %s",
                date('Y-m-d H:i:s'),
                $phoneNumber
            );
            return false;
        }
    }
    
    /**
     * Construire le message SMS court (extrait du template)
     * @param Invoice $invoice Facture
     * @return string Message SMS
     */
    private function buildSmsMessage(Invoice $invoice): string
    {
        // Message court pour l'envoi réel
        return sprintf(
            "Facture %s: Montant rendu %s€. Consultez votre facture sur notre site. Merci!",
            $invoice->getInvoiceNumber(),
            number_format($invoice->getAmountReturned(), 2, ',', ' ')
        );
    }
    
    /**
     * Extraire un numéro de téléphone fictif depuis l'email
     * (En production, récupérer depuis une base de données)
     * @param string $email Email
     * @return string Numéro de téléphone
     */
    private function extractPhoneFromEmail(string $email): string
    {
        // Simulation : générer un numéro fictif basé sur l'email
        // En production, récupérer le vrai numéro depuis la base de données
        $hash = substr(md5($email), 0, 8);
        return '+33 6 ' . substr($hash, 0, 2) . ' ' . substr($hash, 2, 2) . ' ' . substr($hash, 4, 2) . ' ' . substr($hash, 6, 2);
    }
    
    /**
     * Simuler l'envoi d'un SMS
     * @param string $to Numéro de téléphone
     * @param string $message Message
     * @param Invoice $invoice Facture
     * @return bool True si succès
     */
    private function simulateSmsSending(string $to, string $message, Invoice $invoice): bool
    {
        // En développement, on enregistre le SMS dans un fichier log
        $logDir = __DIR__ . '/../../storage/sms';
        
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/sms_' . date('Y-m-d_H-i-s') . '_' . $invoice->getInvoiceNumber() . '.txt';
        
        // Utiliser le template SMS (qui contient le log complet)
        $smsContent = $invoice->toSmsText($to);
        
        return file_put_contents($logFile, $smsContent) !== false;
        
        // En production, utiliser une API SMS :
        // 
        // Exemple avec Twilio :
        // $client = new \Twilio\Rest\Client($accountSid, $authToken);
        // $client->messages->create($to, ['from' => $fromNumber, 'body' => $message]);
        //
        // Exemple avec OVH :
        // $api = new \Ovh\Api($applicationKey, $applicationSecret, $endpoint, $consumerKey);
        // $api->post('/sms/{serviceName}/jobs', ['message' => $message, 'receivers' => [$to]]);
    }
}

