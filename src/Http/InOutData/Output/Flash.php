<?php

/**
 * Gestor de missatges flash mitjançant sessió
 *
 * Permet afegir missatges temporals (èxit, error, etc.)
 * que es mostren una sola vegada a la següent petició.
 */
class FlashMessages
{
    /**
     * Afegeix un missatge flash a la sessió
     *
     * @param string $type Tipus de missatge (success, danger, etc.)
     * @param string $message Text del missatge
     */
    public static function add(string $type, string $message): void
    {
        $_SESSION['flash'][] = compact('type', 'message');
    }

    /**
     * Afegeix un missatge d'èxit
     *
     * @param string $msg
     */
    public static function success(string $msg): void
    {
        self::add('success', $msg);
    }

    /**
     * Afegeix un missatge d'error
     *
     * @param string $msg
     */
    public static function error(string $msg): void
    {
        self::add('danger', $msg);
    }

    /**
     * Mostra tots els missatges flash i els elimina de la sessió
     */
    public static function display(): void
    {
        if (empty($_SESSION['flash'])) {
            return;
        }

        foreach ($_SESSION['flash'] as $flash) {
            echo "<div class='alert alert-{$flash['type']}'>{$flash['message']}</div>";
        }

        // Eliminem els missatges un cop mostrats
        unset($_SESSION['flash']);
    }
}