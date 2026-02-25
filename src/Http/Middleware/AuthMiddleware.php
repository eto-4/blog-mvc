<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Session\Session;
use App\Http\Routing\Redirect;

/**
 * AuthMiddleware
 *
 * Protegeix rutes que requereixen autenticació.
 * Si l'usuari no està autenticat, redirigeix a /login.
 *
 * Ús al Router (futur):
 *   $router->get('/my-posts', [PostController::class, 'myPosts'])
 *          ->middleware(AuthMiddleware::class);
 *
 * Ús manual als controllers (actual):
 *   AuthMiddleware::handle();
 */
class AuthMiddleware
{
    /**
     * Comprova que l'usuari està autenticat.
     * Redirigeix a /login si no ho està.
     */
    public static function handle(): void
    {
        if (!Session::has('user_id')) {
            Redirect::withError('/login', 'Has d\'iniciar sessió per accedir a aquesta pàgina.');
        }
    }

    /**
     * Retorna true si l'usuari està autenticat, false si no.
     * Útil per comprovar sense redirigir.
     */
    public static function check(): bool
    {
        return Session::has('user_id');
    }
}