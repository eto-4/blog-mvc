<?php

declare(strict_types=1);

namespace App\Infrastructure\Routing;

/**
 * Helper per gestionar redireccions HTTP de forma centralitzada.
 */
class Redirect
{
    /**
     * Redirigeix a una URL relativa al BASE_PATH.
     *
     * @param string $path    Ruta relativa (ex: '/login', '/my-posts')
     * @param int    $code    Codi HTTP (301 permanent, 302 temporal)
     */
    public static function to(string $path, int $code = 302): never
    {
        http_response_code($code);
        header('Location: ' . BASE_PATH . $path);
        exit;
    }

    /**
     * Redirigeix a una URL absoluta.
     *
     * @param string $url   URL completa (ex: 'https://example.com')
     * @param int    $code
     */
    public static function away(string $url, int $code = 302): never
    {
        http_response_code($code);
        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirigeix enrere (a la pàgina anterior via HTTP_REFERER).
     * Si no hi ha referer, redirigeix a la ruta per defecte.
     *
     * @param string $fallback  Ruta per defecte si no hi ha referer
     */
    public static function back(string $fallback = '/'): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;

        if ($referer) {
            http_response_code(302);
            header('Location: ' . $referer);
        } else {
            self::to($fallback);
        }

        exit;
    }

    /**
     * Redirigeix amb un missatge flash d'èxit.
     *
     * @param string $path
     * @param string $message
     */
    public static function withSuccess(string $path, string $message): never
    {
        $_SESSION['flash_success'] = $message;
        self::to($path);
    }

    /**
     * Redirigeix amb un missatge flash d'error.
     *
     * @param string $path
     * @param string $message
     */
    public static function withError(string $path, string $message): never
    {
        $_SESSION['flash_error'] = $message;
        self::to($path);
    }
} 