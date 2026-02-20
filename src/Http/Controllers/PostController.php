<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Models\Post;
use App\Domain\Slug\SlugGenerator;
use App\Http\Session\Session;
use App\Infrastructure\Security\Csrf;

/**
 * PostController
 *
 * Gestiona totes les accions relacionades amb els posts.
 *
 * Rutes públiques:
 *   GET  /posts                  → index()
 *   GET  /posts/{slug}           → showPost(string $slug)
 *   GET  /author/{id}            → showAuthor(string $id)
 *   GET  /search                 → search()
 *
 * Rutes autenticades (/my-posts):
 *   GET  /my-posts               → myPosts()
 *   GET  /my-posts/create        → create()
 *   POST /my-posts               → store()
 *   GET  /my-posts/{id}/edit     → edit(string $id)
 *   POST /my-posts/{id}/update   → update(string $id)
 *   POST /my-posts/{id}/delete   → delete(string $id)
 *   POST /my-posts/{id}/publish  → publish(string $id)
 */
class PostController
{
    // -------------------------------------------------------------------------
    // Helpers privats
    // -------------------------------------------------------------------------

    /**
     * Redirigeix a login si l'usuari no està autenticat.
     * Substitut temporal fins que AuthMiddleware estigui implementat.
     */
    private function requireAuth(): void
    {
        if (!Session::has('user_id')) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }
    }

    /**
     * Comprova que el post existeix i que l'usuari en és l'autor.
     *
     * @throws void  Fa exit() directament si no passa la validació.
     */
    private function requireOwner(int $id): Post
    {
        $post = new Post();

        if (!$post->load($id)) {
            http_response_code(404);
            require APP_ROOT . '/src/Views/home/404.php';
            exit;
        }

        if ((int) $post->author_id !== (int) Session::get('user_id')) {
            http_response_code(403);
            require APP_ROOT . '/src/Views/home/404.php';
            exit;
        }

        return $post;
    }

    /**
     * Carrega header + vista + footer passant variables a la vista.
     *
     * @param string               $view  Ruta relativa des de src/Views (ex: 'posts/index')
     * @param array<string, mixed> $data  Variables disponibles a la vista
     */
    private function render(string $view, array $data = []): void
    {
        extract($data);
        require APP_ROOT . '/src/Views/layouts/header.php';
        require APP_ROOT . "/src/Views/{$view}.php";
        require APP_ROOT . '/src/Views/layouts/footer.php';
    }

    // -------------------------------------------------------------------------
    // Accions públiques
    // -------------------------------------------------------------------------

    /**
     * GET /posts
     * Llistat de tots els posts publicats amb paginació.
     */
    public function index(): void
    {
        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $result = Post::findAllPaginated(null, $page, 10);

        $this->render('posts/index', [
            'posts'       => $result['posts'],
            'totalPages'  => $result['pages'],
            'currentPage' => $page,
        ]);
    }

    /**
     * GET /posts/{slug}
     * Visualitza un post individual per slug.
     */
    public function showPost(string $slug): void
    {
        $post = new Post();

        if (!$post->loadBySlug($slug)) {
            http_response_code(404);
            require APP_ROOT . '/src/Views/home/404.php';
            exit;
        }

        if ($post->status !== 'published') {
            http_response_code(404);
            require APP_ROOT . '/src/Views/home/404.php';
            exit;
        }

        $post->incrementViews();

        $this->render('posts/show', ['post' => $post]);
    }

    /**
     * GET /author/{id}
     * Llistat de posts publicats d'un autor específic, amb paginació.
     */
    public function showAuthor(string $id): void
    {
        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $result = Post::findByAuthorPaginated(null, (int) $id, $page, 10);

        $this->render('posts/index', [
            'posts'       => $result['posts'],
            'totalPages'  => $result['pages'],
            'currentPage' => $page,
        ]);
    }

    /**
     * GET /search?q=...
     * Cerca posts per títol, contingut o excerpt.
     */
    public function search(): void
    {
        $query = trim($_GET['q'] ?? '');
        $posts = [];

        if ($query !== '') {
            $posts = Post::search(null, $query);
        }

        $this->render('posts/index', [
            'posts'       => $posts,
            'totalPages'  => 1,
            'currentPage' => 1,
            'searchQuery' => $query,
        ]);
    }

    // -------------------------------------------------------------------------
    // Accions autenticades — gestió pròpia de posts
    // -------------------------------------------------------------------------

    /**
     * GET /my-posts
     * Llistat de tots els posts de l'usuari autenticat (tots els estats).
     */
    public function myPosts(): void
    {
        $this->requireAuth();

        $posts = Post::findByAuthor(null, (int) Session::get('user_id'));

        $this->render('posts/my-posts', ['posts' => $posts]);
    }

    /**
     * GET /my-posts/create
     * Formulari per crear un nou post.
     */
    public function create(): void
    {
        $this->requireAuth();

        $this->render('posts/create', [
            'csrfToken' => Csrf::generate(),
        ]);
    }

    /**
     * POST /my-posts
     * Processa el formulari de creació d'un nou post.
     */
    public function store(): void
    {
        $this->requireAuth();
        Csrf::validate();

        $title   = trim($_POST['title']   ?? '');
        $content = trim($_POST['content'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $status  = $_POST['status'] ?? 'draft';

        $errors = $this->validatePost($title, $content);
        if (!empty($errors)) {
            $this->render('posts/create', [
                'errors'    => $errors,
                'old'       => $_POST,
                'csrfToken' => Csrf::generate(),
            ]);
            return;
        }

        $post               = new Post();
        $post->title        = $title;
        $post->content      = $content;
        $post->excerpt      = $excerpt !== '' ? $excerpt : $this->generateExcerpt($content);
        $post->author_id    = (int) Session::get('user_id');
        $post->status       = in_array($status, ['draft', 'published', 'archived'], true) ? $status : 'draft';
        $post->slug         = SlugGenerator::generate($title);
        $post->published_at = $post->status === 'published' ? date('Y-m-d H:i:s') : null;

        $post->save();

        Session::set('flash_success', 'Post creat correctament.');
        header('Location: ' . BASE_PATH . '/my-posts');
        exit;
    }

    /**
     * GET /my-posts/{id}/edit
     * Formulari d'edició d'un post de l'autor autenticat.
     */
    public function edit(string $id): void
    {
        $this->requireAuth();
        $post = $this->requireOwner((int) $id);

        $this->render('posts/edit', [
            'post'      => $post,
            'csrfToken' => Csrf::generate(),
        ]);
    }

    /**
     * POST /my-posts/{id}/update
     * Processa el formulari d'edició d'un post.
     */
    public function update(string $id): void
    {
        $this->requireAuth();
        Csrf::validate();
        $post = $this->requireOwner((int) $id);

        $title   = trim($_POST['title']   ?? '');
        $content = trim($_POST['content'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $status  = $_POST['status'] ?? $post->status;

        $errors = $this->validatePost($title, $content);
        if (!empty($errors)) {
            $this->render('posts/edit', [
                'errors'    => $errors,
                'post'      => $post,
                'old'       => $_POST,
                'csrfToken' => Csrf::generate(),
            ]);
            return;
        }

        // Regenerar slug només si el títol ha canviat
        if ($post->title !== $title) {
            $post->slug = SlugGenerator::generate($title);
        }

        $post->title   = $title;
        $post->content = $content;
        $post->excerpt = $excerpt !== '' ? $excerpt : $this->generateExcerpt($content);
        $post->status  = in_array($status, ['draft', 'published', 'archived'], true) ? $status : $post->status;

        // Assignar published_at només la primera vegada que es publica
        if ($post->status === 'published' && $post->published_at === null) {
            $post->published_at = date('Y-m-d H:i:s');
        }

        $post->update();

        Session::set('flash_success', 'Post actualitzat correctament.');
        header('Location: ' . BASE_PATH . '/my-posts');
        exit;
    }

    /**
     * POST /my-posts/{id}/delete
     * Elimina un post de l'autor autenticat.
     */
    public function delete(string $id): void
    {
        $this->requireAuth();
        Csrf::validate();
        $post = $this->requireOwner((int) $id);

        $post->delete();

        Session::set('flash_success', 'Post eliminat correctament.');
        header('Location: ' . BASE_PATH . '/my-posts');
        exit;
    }

    /**
     * POST /my-posts/{id}/publish
     * Toggle: published → draft / draft|archived → published.
     */
    public function publish(string $id): void
    {
        $this->requireAuth();
        Csrf::validate();
        $post = $this->requireOwner((int) $id);

        if ($post->status === 'published') {
            $post->status       = 'draft';
            $post->published_at = null;
        } else {
            $post->status       = 'published';
            $post->published_at = date('Y-m-d H:i:s');
        }

        $post->update();

        Session::set('flash_success', 'Estat del post actualitzat.');
        header('Location: ' . BASE_PATH . '/my-posts');
        exit;
    }

    // -------------------------------------------------------------------------
    // Helpers de negoci privats
    // -------------------------------------------------------------------------

    /**
     * Valida els camps obligatoris d'un post.
     *
     * @return array<string, string> Errors indexats per camp
     */
    private function validatePost(string $title, string $content): array
    {
        $errors = [];

        if (mb_strlen($title) < 5) {
            $errors['title'] = 'El títol ha de tenir mínim 5 caràcters.';
        } elseif (mb_strlen($title) > 200) {
            $errors['title'] = 'El títol no pot superar els 200 caràcters.';
        }

        if (mb_strlen($content) < 50) {
            $errors['content'] = 'El contingut ha de tenir mínim 50 caràcters.';
        } elseif (mb_strlen($content) > 10000) {
            $errors['content'] = 'El contingut no pot superar els 10.000 caràcters.';
        }

        return $errors;
    }

    /**
     * Genera un excerpt automàtic a partir del contingut (màx. 200 caràcters).
     */
    private function generateExcerpt(string $content): string
    {
        $plain = strip_tags($content);
        return mb_strlen($plain) > 200
            ? mb_substr($plain, 0, 200) . '…'
            : $plain;
    }
}