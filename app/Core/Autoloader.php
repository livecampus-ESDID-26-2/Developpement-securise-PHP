<?php

namespace App\Core;

/**
 * Autoloader PSR-4
 */
class Autoloader
{
    /**
     * Enregistrer l'autoloader
     * @return void
     */
    public static function register(): void
    {
        spl_autoload_register([__CLASS__, 'autoload']);
    }
    
    /**
     * Charger automatiquement une classe
     * @param string $class Nom complet de la classe (avec namespace)
     * @return void
     */
    private static function autoload(string $class): void
    {
        // Namespace de base de l'application
        $prefix = 'App\\';
        
        // Vérifier si la classe utilise le namespace de base
        if (strpos($class, $prefix) !== 0) {
            return;
        }
        
        // Retirer le prefix du nom de la classe
        $relativeClass = substr($class, strlen($prefix));
        
        // Remplacer les namespace séparateurs par des séparateurs de dossiers
        $file = ROOT_PATH . '/' . str_replace('\\', '/', $relativeClass) . '.php';
        
        // Si le fichier existe, le charger
        if (file_exists($file)) {
            require_once $file;
        }
    }
}

