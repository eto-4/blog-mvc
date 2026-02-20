<?php

declare(strict_types=1);

use App\Infrastructure\Routing\Router;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/**
 * Fitxer de definició de rutes de l'aplicació.
 *
 * Retorna una funció que rep el Router i registra totes les rutes.
 */
return function (Router $router): void {
    // Ruta principal de la pàgina home
    $router->get('/', [HomeController::class, 'index']);

    // Rutes publiques
    $router->get('/posts', [PostController::class, 'index']);
    $router->get('/posts/{slug}', [PostController::class, 'showPost']);
    $router->get('/author/{id}', [PostController::class, 'showAuthor']);
    $router->get('/search', [PostController::class, 'search']);

    // Rutes Auth
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
    $router->post('/logout', [AuthController::class, 'logout']);

    // Rutes de gestió de posts (Auth required)
    $router->get('/my-posts', [PostController::class, 'myPosts']);
    $router->get('/my-posts/create', [PostController::class, 'create']);
    $router->post('/my-posts', [PostController::class, 'store']);
    $router->get('/my-posts/{id}/edit', [PostController::class, 'edit']);
    $router->post('/my-posts/{id}/update', [PostController::class, 'update']);
    $router->post('/my-posts/{id}/delete', [PostController::class, 'delete']);
    $router->post('/my-posts/{id}/publish', [PostController::class, 'publish']);

    // Rutes Perfil Usuari
    $router->get('/profile', [UserController::class, 'userProfile']);
    $router->get('/profile/edit', [UserController::class, 'userEditProfile']);
    $router->post('/profile/update', [UserController::class, 'update']);
    $router->post('/profile/avatar', [UserController::class, 'avatar']);

    // Rutes d'administració
    $router->get('/admin', [UserController::class, 'adminIndex']);
    $router->get('/admin/users', [UserController::class, 'adminUserList']);
    $router->get('/admin/posts', [UserController::class, 'adminPosts']);
    $router->post('/admin/users/{id}/delete', [UserController::class, 'adminDeleteUser']);
    $router->post('/admin/posts/{id}/status', [UserController::class, 'adminSwitchStatus']);


};