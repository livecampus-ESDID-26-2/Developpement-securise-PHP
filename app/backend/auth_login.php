<?php
/**
 * Traitement de l'authentification
 */

// Démarrage de la session
session_start();

// Chargement de la configuration
if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/../config/config.php';
}

// Redirection si déjà connecté
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: ../index.php');
    }
    exit;
}

// Vérification que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// Récupération des données du formulaire
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validation basique
if (empty($email) || empty($password)) {
    $erreur = "Veuillez remplir tous les champs.";
    require_once VIEWS_PATH . '/login.php';
    exit;
}

try {
    $pdo = getDbConnection();
    
    // Requête préparée pour éviter les injections SQL
    $stmt = $pdo->prepare("SELECT id, email, password, role FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    
    // Vérification de l'utilisateur et du mot de passe
    // NOTE: En production, utiliser password_hash() et password_verify()
    if ($user && $user['password'] === $password) {
        // Création de la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['connected'] = true;
        
        // Redirection selon le rôle
        if ($user['role'] === 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: ../index.php');
        }
        exit;
    } else {
        $erreur = "Email ou mot de passe incorrect.";
        require_once VIEWS_PATH . '/login.php';
        exit;
    }
} catch (PDOException $e) {
    error_log("Erreur de connexion : " . $e->getMessage());
    $erreur = "Erreur de connexion à la base de données.";
    require_once VIEWS_PATH . '/login.php';
    exit;
}

