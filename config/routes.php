<?php

declare(strict_types=1);

use App\Infrastructure\Routing\Router;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;

/**
 * Fitxer de definició de rutes de l'aplicació.
 *
 * Retorna una funció que rep el Router i registra totes les rutes.
 */
return function (Router $router): void {
    // Ruta principal de la pàgina home
    $router->get('/', [HomeController::class, 'index']);

    // Rutes de tasques/posts (rutes temporals mentre migrem a l'esquema definitiu)
    $router->get('/tasques', [PostController::class, 'index']);
    $router->get('/tasques/create', [PostController::class, 'create']);
    $router->post('/tasques', [PostController::class, 'store']);
    $router->get('/tasques/{id}/edit', [PostController::class, 'edit']);
    $router->post('/tasques/{id}', [PostController::class, 'update']);
    $router->post('/tasques/{id}/delete', [PostController::class, 'delete']);

    // Rutes d'autenticació bàsica
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
    $router->post('/logout', [AuthController::class, 'logout']);
};