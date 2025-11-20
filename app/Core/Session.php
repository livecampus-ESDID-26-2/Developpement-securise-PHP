<?php

namespace App\Core;

/**
 * Classe Session - Gestion des sessions
 */
class Session
{
    /**
     * Démarrer la session
     * @return void
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Définir une valeur en session
     * @param string $key Clé
     * @param mixed $value Valeur
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Obtenir une valeur de session
     * @param string $key Clé
     * @param mixed $default Valeur par défaut si la clé n'existe pas
     * @return mixed Valeur ou défaut
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Vérifier si une clé existe en session
     * @param string $key Clé
     * @return bool True si existe, false sinon
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Supprimer une valeur de session
     * @param string $key Clé
     * @return void
     */
    public static function delete(string $key): void
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Détruire toute la session
     * @return void
     */
    public static function destroy(): void
    {
        self::start();
        session_unset();
        session_destroy();
    }
    
    /**
     * Obtenir toutes les données de session
     * @return array Données de session
     */
    public static function all(): array
    {
        self::start();
        return $_SESSION;
    }
    
    /**
     * Définir un message flash
     * @param string $key Clé
     * @param mixed $value Valeur
     * @return void
     */
    public static function flash(string $key, $value): void
    {
        self::set('flash_' . $key, $value);
    }
    
    /**
     * Obtenir et supprimer un message flash
     * @param string $key Clé
     * @param mixed $default Valeur par défaut
     * @return mixed Valeur ou défaut
     */
    public static function getFlash(string $key, $default = null)
    {
        $value = self::get('flash_' . $key, $default);
        self::delete('flash_' . $key);
        return $value;
    }
}

