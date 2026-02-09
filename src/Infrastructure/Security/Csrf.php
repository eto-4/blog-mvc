<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    /**
     * Genera o retorna el token CSRF actual
     */
    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Retorna el camp hidden per als formularis
     */
    public static function field(): string
    {
        $token = self::token();
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES) . '">';
    }

    /**
     * Valida el token CSRF rebut per POST
     */
    public static function validate(): bool
    {
        if (
            empty($_POST['_csrf']) ||
            empty($_SESSION[self::SESSION_KEY])
        ) {
            return false;
        }

        return hash_equals(
            $_SESSION[self::SESSION_KEY],
            $_POST['_csrf']
        );
    }

    /**
     * Força regeneració del token (opcional)
     */
    public static function regenerate(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }
}