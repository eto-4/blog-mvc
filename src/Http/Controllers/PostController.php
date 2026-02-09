<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use PDO;
use Psr\Log\LoggerInterface;

/**
 * Controlador de posts
 *
 * (Aquest controlador encara està pendent de refactoritzar completament
 * per adaptar-se als nous models i serveis. De moment mantenim la signatura
 * bàsica perquè el router pugui cridar-lo.)
 */
class PostController
{
    // TODO: Aquest controlador s'implementarà en la missió de CRUD de posts.
    // De moment mantenim una implementació mínima perquè les rutes no trenquin.

    public function index(): void
    {
        require APP_ROOT . '/src/Views/layouts/header.php';
        require APP_ROOT . '/src/Views/posts/index.php';
        require APP_ROOT . '/src/Views/layouts/footer.php';
    }

    public function create(): void
    {
        require APP_ROOT . '/src/Views/layouts/header.php';
        require APP_ROOT . '/src/Views/posts/create.php';
        require APP_ROOT . '/src/Views/layouts/footer.php';
    }

    public function edit(int $id): void
    {
        // Implementació temporal: només carrega la vista d'edició
        require APP_ROOT . '/src/Views/layouts/header.php';
        require APP_ROOT . '/src/Views/posts/edit.php';
        require APP_ROOT . '/src/Views/layouts/footer.php';
    }

    public function store(): void
    {
        // Implementació temporal
        header('Location:' . BASE_PATH . '/tasques');
        exit;
    }

    public function update(int $id): void
    {
        // Implementació temporal
        header('Location:' . BASE_PATH . '/tasques');
        exit;
    }

    public function delete(int $id): void
    {
        // Implementació temporal
        header('Location:' . BASE_PATH . '/tasques');
        exit;
    }
}