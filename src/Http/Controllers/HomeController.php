<?php

/**
 * Controlador principal de la pàgina d'inici
 *
 * S'encarrega de mostrar la vista principal de l'aplicació
 */
class HomeController
{
    /**
     * Mostra la pàgina d'inici
     *
     * Carrega el layout bàsic: capçalera, contingut principal i peu
     */
    public function index(): void
    {
        // Capçalera comuna de l'aplicació
        require APP_ROOT . '/views/layouts/header.php';

        // Vista principal de la pàgina d'inici
        require APP_ROOT . '/views/home/index.php';

        // Peu de pàgina comú
        require APP_ROOT . '/views/layouts/footer.php';
    }
}
