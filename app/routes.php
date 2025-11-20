<?php
/**
 * Définition des routes de l'application
 */

use App\Controllers\AuthController;
use App\Controllers\CashRegisterController;
use App\Controllers\AdminController;

// Routes d'authentification
$router->get('/', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Routes de la caisse (utilisateurs)
$router->get('/cash-register', [CashRegisterController::class, 'index']);
$router->post('/cash-register/process', [CashRegisterController::class, 'process']);
$router->get('/cash-register/history', [CashRegisterController::class, 'history']);

// Routes d'administration
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/admin/history', [AdminController::class, 'history']);
$router->get('/admin/user/{userId}', [AdminController::class, 'userDetail']);

