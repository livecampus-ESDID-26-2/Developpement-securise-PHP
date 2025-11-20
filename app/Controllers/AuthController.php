<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

/**
 * Contrôleur d'authentification
 */
class AuthController extends Controller
{
    private User $userModel;
    
    public function __construct()
    {
        $this->userModel = new User();
    }
    
    /**
     * Afficher la page de connexion
     * @return void
     */
    public function showLogin(): void
    {
        // Si déjà connecté, rediriger
        if ($this->isLoggedIn()) {
            if ($this->isAdmin()) {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/cash-register');
            }
            return;
        }
        
        $erreur = Session::getFlash('erreur');
        $this->view('login', ['erreur' => $erreur]);
    }
    
    /**
     * Traiter la connexion
     * @return void
     */
    public function login(): void
    {
        // Vérifier la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            return;
        }
        
        // Récupération des données
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validation basique
        if (empty($email) || empty($password)) {
            Session::flash('erreur', 'Veuillez remplir tous les champs.');
            $this->redirect('/');
            return;
        }
        
        // Authentification
        $user = $this->userModel->authenticate($email, $password);
        
        if ($user) {
            // Créer la session
            Session::set('user_id', $user['id']);
            Session::set('user_email', $user['email']);
            Session::set('user_role', $user['role']);
            Session::set('connected', true);
            
            // Redirection selon le rôle
            if ($user['role'] === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/cash-register');
            }
        } else {
            Session::flash('erreur', 'Email ou mot de passe incorrect.');
            $this->redirect('/');
        }
    }
    
    /**
     * Déconnexion
     * @return void
     */
    public function logout(): void
    {
        Session::destroy();
        $this->redirect('/');
    }
}

