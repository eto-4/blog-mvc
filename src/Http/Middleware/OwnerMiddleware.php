<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Models\Post;
use App\Http\Session\Session;
use App\Infrastructure\Routing\Redirect;

/**
 * OwnerMiddleware
 *
 * Verifica que l'usuari autenticat és l'autor del post sol·licitat.
 * Requereix que AuthMiddleware s'hagi executat primer.
 *
 * Ús manual als controllers:
 *   $post = OwnerMiddleware::handle($id);
 */
class OwnerMiddleware
{
    /**
     * Comprova que el post existeix i que l'usuari n'és l'autor.
     *
     * @param  int  $postId  ID del post a verificar.
     * @return Post          Post carregat si passa la validació.
     *
     * Redirigeix a 404 si el post no existeix.
     * Redirigeix a /my-posts amb error si l'usuari no és l'autor.
     */
    public static function handle(int $postId): Post
    {
        $post = new Post();

        if (!$post->load($postId)) {
            http_response_code(404);
            require APP_ROOT . '/src/Views/home/404.php';
            exit;
        }

        if ((int) $post->author_id !== (int) Session::get('user_id')) {
            Redirect::withError('/my-posts', 'No tens permís per modificar aquest post.');
        }

        return $post;
    }

    /**
     * Retorna true si l'usuari és l'autor del post, false si no.
     * Útil per comprovar sense redirigir (ex: mostrar botons d'edició).
     *
     * @param int $authorId  author_id del post
     */
    public static function check(int $authorId): bool
    {
        return (int) Session::get('user_id') === $authorId;
    }
}