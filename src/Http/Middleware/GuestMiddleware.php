<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Session\Session;
use App\Infrastructure\Routing\Redirect;

/**
 * GuestMiddleware
 *
 * Protegeix rutes que només han de ser accessibles per usuaris NO autenticats.
 * Si l'usuari ja està autenticat, redirigeix a la pàgina principal.
 *
 * Aplicar a: /login, /register
 *
 * Ús manual als controllers:
 *   GuestMiddleware::handle();
 */
class GuestMiddleware
{
    /**
     * Comprova que l'usuari NO està autenticat.
     * Redirigeix a / si ja ho està.
     */
    public static function handle(): void
    {
        if (Session::has('user_id')) {
            Redirect::to('/');
        }
    }

    /**
     * Retorna true si l'usuari és un convidat (no autenticat).
     */
    public static function check(): bool
    {
        return !Session::has('user_id');
    }
}