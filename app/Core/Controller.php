<?php

namespace App\Core;

/**
 * Classe Controller - Classe de base pour tous les contrôleurs
 */
abstract class Controller
{
    /**
     * Charger une vue avec des données
     * @param string $view Nom de la vue (sans .php)
     * @param array $data Données à passer à la vue
     * @return void
     */
    protected function view(string $view, array $data = []): void
    {
        // Extraction des données pour les rendre disponibles dans la vue
        extract($data);
        
        // Chemin vers la vue
        $viewPath = ROOT_PATH . '/views/' . $view . '.php';
        
        // Vérification que la vue existe
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            throw new \Exception("La vue '{$view}' n'existe pas.");
        }
    }
    
    /**
     * Rediriger vers une URL
     * @param string $url URL de redirection
     * @return void
     */
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }
    
    /**
     * Retourner une réponse JSON
     * @param mixed $data Données à retourner
     * @param int $statusCode Code HTTP
     * @return void
     */
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Vérifier si l'utilisateur est connecté
     * @return bool True si connecté, false sinon
     */
    protected function isLoggedIn(): bool
    {
        return Session::has('connected') && Session::get('connected') === true;
    }
    
    /**
     * Vérifier si l'utilisateur est un admin
     * @return bool True si admin, false sinon
     */
    protected function isAdmin(): bool
    {
        return $this->isLoggedIn() && Session::get('user_role') === 'admin';
    }
    
    /**
     * Exiger une authentification
     * @return void
     */
    protected function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/');
        }
    }
    
    /**
     * Exiger un rôle administrateur
     * @return void
     */
    protected function requireAdmin(): void
    {
        $this->requireLogin();
        
        if (!$this->isAdmin()) {
            $this->redirect('/');
        }
    }
    
    /**
     * Obtenir l'utilisateur connecté
     * @return array|null Informations de l'utilisateur ou null
     */
    protected function getCurrentUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => Session::get('user_id'),
            'email' => Session::get('user_email'),
            'role' => Session::get('user_role')
        ];
    }
}

