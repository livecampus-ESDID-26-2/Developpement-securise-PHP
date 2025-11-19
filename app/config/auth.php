<?php
/**
 * Fonctions d'authentification et de vérification des sessions
 */

/**
 * Vérifier si l'utilisateur est connecté
 * @return bool True si connecté, false sinon
 */
function isLoggedIn(): bool {
    return isset($_SESSION['connected']) && $_SESSION['connected'] === true;
}

/**
 * Vérifier si l'utilisateur est un admin
 * @return bool True si admin, false sinon
 */
function isAdmin(): bool {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Obtenir l'ID de l'utilisateur connecté
 * @return int|null ID de l'utilisateur ou null si non connecté
 */
function getUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtenir l'email de l'utilisateur connecté
 * @return string|null Email de l'utilisateur ou null si non connecté
 */
function getUserEmail(): ?string {
    return $_SESSION['user_email'] ?? null;
}

/**
 * Obtenir le rôle de l'utilisateur connecté
 * @return string|null Rôle de l'utilisateur ou null si non connecté
 */
function getUserRole(): ?string {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Rediriger vers la page de login si non connecté
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ../index.php');
        exit;
    }
}

/**
 * Rediriger vers la page de login si non admin
 */
function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ../index.php');
        exit;
    }
}

/**
 * Obtenir les informations de l'utilisateur connecté
 * @return array|null Informations de l'utilisateur ou null
 */
function getCurrentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => getUserId(),
        'email' => getUserEmail(),
        'role' => getUserRole()
    ];
}

