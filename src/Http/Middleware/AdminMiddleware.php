<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Session\Session;
use App\Infrastructure\Routing\Redirect;

/**
 * AdminMiddleware
 *
 * Protegeix rutes que requereixen rol d'administrador.
 * Requereix que AuthMiddleware s'hagi executat primer.
 *
 * Ús manual als controllers:
 *   AdminMiddleware::handle();
 */
class AdminMiddleware
{
    /**
     * Comprova que l'usuari està autenticat i té rol 'admin'.
     * Redirigeix a / amb error si no té permisos.
     */
    public static function handle(): void
    {
        if (!Session::has('user_id')) {
            Redirect::withError('/login', 'Has d\'iniciar sessió per accedir a aquesta pàgina.');
        }

        if (Session::get('user_role') !== 'admin') {
            Redirect::withError('/', 'No tens permisos per accedir a aquesta pàgina.');
        }
    }

    /**
     * Retorna true si l'usuari és admin.
     */
    public static function check(): bool
    {
        return Session::has('user_id') && Session::get('user_role') === 'admin';
    }
}