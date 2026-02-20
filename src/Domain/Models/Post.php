<?php

declare(strict_types=1);

namespace App\Domain\Models;

use App\Infrastructure\Database\DatabaseCore\Database;
use PDO;

/**
 * Model de Post
 *
 * Gestiona les operacions CRUD i les consultes relacionades amb els posts.
 */
class Post
{
    private PDO $pdo;

    public ?int $id           = null;
    public ?string $title     = null;
    public ?string $slug      = null;
    public ?string $content   = null;
    public ?string $excerpt   = null;
    public ?string $featured_image = null;
    public ?int $author_id    = null;
    public string $status     = 'draft';
    public int $views_count   = 0;
    public ?string $published_at = null;
    public ?string $created_at   = null;
    public ?string $updated_at   = null;

    /**
     * @param PDO|null $pdo Connexió PDO. Si és null, s'utilitza el Singleton.
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    // -------------------------------------------------------------------------
    // Operacions d'instància (sobre UN post)
    // -------------------------------------------------------------------------

    /**
     * Carrega un post per ID i omple les propietats de la instància.
     *
     * @param int $id
     * @return bool True si el post existeix, false si no.
     */
    public function load(int $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($post) {
            foreach ($post as $key => $value) {
                $this->$key = $value;
            }
            return true;
        }

        return false;
    }

    /**
     * Carrega un post per slug i omple les propietats de la instància.
     *
     * @param string $slug
     * @return bool True si el post existeix, false si no.
     */
    public function loadBySlug(string $slug): bool
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug' => $slug]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($post) {
            foreach ($post as $key => $value) {
                $this->$key = $value;
            }
            return true;
        }

        return false;
    }

    /**
     * Desa un nou post i assigna l'ID generat a $this->id.
     *
     * @return void
     */
    public function save(): void
    {
        $sql = 'INSERT INTO posts 
                    (title, slug, content, excerpt, featured_image, author_id, status, views_count, published_at)
                VALUES
                    (:title, :slug, :content, :excerpt, :featured_image, :author_id, :status, :views_count, :published_at)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':title'          => $this->title,
            ':slug'           => $this->slug,
            ':content'        => $this->content,
            ':excerpt'        => $this->excerpt,
            ':featured_image' => $this->featured_image,
            ':author_id'      => $this->author_id,
            ':status'         => $this->status,
            ':views_count'    => $this->views_count,
            ':published_at'   => $this->published_at,
        ]);

        $this->id = (int) $this->pdo->lastInsertId();
    }

    /**
     * Actualitza el post existent a la base de dades.
     *
     * @return void
     */
    public function update(): void
    {
        $sql = 'UPDATE posts SET
                    title          = :title,
                    slug           = :slug,
                    content        = :content,
                    excerpt        = :excerpt,
                    featured_image = :featured_image,
                    author_id      = :author_id,
                    status         = :status,
                    views_count    = :views_count,
                    published_at   = :published_at,
                    updated_at     = NOW()
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'             => $this->id,
            ':title'          => $this->title,
            ':slug'           => $this->slug,
            ':content'        => $this->content,
            ':excerpt'        => $this->excerpt,
            ':featured_image' => $this->featured_image,
            ':author_id'      => $this->author_id,
            ':status'         => $this->status,
            ':views_count'    => $this->views_count,
            ':published_at'   => $this->published_at,
        ]);
    }

    /**
     * Elimina el post de la base de dades.
     *
     * @return void
     */
    public function delete(): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM posts WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
    }

    /**
     * Incrementa el comptador de visualitzacions en 1.
     *
     * @return void
     */
    public function incrementViews(): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE posts SET views_count = views_count + 1 WHERE id = :id'
        );
        $stmt->execute([':id' => $this->id]);
        $this->views_count++;
    }

    // -------------------------------------------------------------------------
    // Consultes estàtiques (retornen arrays de dades, no instàncies)
    // -------------------------------------------------------------------------

    /**
     * Retorna tots els posts ordenats per data de creació descendent.
     *
     * @param PDO|null $pdo
     * @return array<int, array<string, mixed>>
     */
    public static function findAll(?PDO $pdo = null): array
    {
        $pdo  = $pdo ?? Database::getInstance()->getConnection();
        $stmt = $pdo->query('SELECT * FROM posts ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna posts publicats amb paginació, incloent dades de l'autor.
     *
     * @param PDO|null $pdo
     * @param int      $page    Pàgina actual (comença en 1)
     * @param int      $perPage Posts per pàgina
     * @return array{posts: array, total: int, pages: int}
     */
    public static function findAllPaginated(?PDO $pdo = null, int $page = 1, int $perPage = 10): array
    {
        $pdo    = $pdo ?? Database::getInstance()->getConnection();
        $offset = ($page - 1) * $perPage;

        // Total de posts publicats
        $countStmt = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'");
        $total     = (int) $countStmt->fetchColumn();

        // Posts de la pàgina actual amb nom de l'autor
        $stmt = $pdo->prepare("
            SELECT p.*, u.name AS author_name, u.avatar AS author_avatar
            FROM posts p
            JOIN users u ON u.id = p.author_id
            WHERE p.status = 'published'
            ORDER BY p.published_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();

        return [
            'posts' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Retorna els posts d'un autor amb paginació.
     *
     * @param PDO|null $pdo
     * @param int      $authorId
     * @param int      $page
     * @param int      $perPage
     * @return array{posts: array, total: int, pages: int}
     */
    public static function findByAuthorPaginated(
        ?PDO $pdo = null,
        int $authorId = 0,
        int $page = 1,
        int $perPage = 10
    ): array {
        $pdo    = $pdo ?? Database::getInstance()->getConnection();
        $offset = ($page - 1) * $perPage;

        $countStmt = $pdo->prepare("
            SELECT COUNT(*) FROM posts 
            WHERE author_id = :author_id AND status = 'published'
        ");
        $countStmt->execute([':author_id' => $authorId]);
        $total = (int) $countStmt->fetchColumn();

        $stmt = $pdo->prepare("
            SELECT p.*, u.name AS author_name, u.avatar AS author_avatar
            FROM posts p
            JOIN users u ON u.id = p.author_id
            WHERE p.author_id = :author_id AND p.status = 'published'
            ORDER BY p.published_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':author_id', $authorId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',     $perPage,  PDO::PARAM_INT);
        $stmt->bindValue(':offset',    $offset,   PDO::PARAM_INT);
        $stmt->execute();

        return [
            'posts' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Retorna els posts d'un autor (tots els estats), per al dashboard personal.
     *
     * @param PDO|null $pdo
     * @param int      $authorId
     * @return array<int, array<string, mixed>>
     */
    public static function findByAuthor(?PDO $pdo = null, int $authorId = 0): array
    {
        $pdo  = $pdo ?? Database::getInstance()->getConnection();
        $stmt = $pdo->prepare('
            SELECT * FROM posts 
            WHERE author_id = :author_id 
            ORDER BY created_at DESC
        ');
        $stmt->execute([':author_id' => $authorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cerca posts per títol, contingut o excerpt.
     * Fixat: usa paràmetres diferents per evitar el bug de PDO amb named params repetits.
     *
     * @param PDO|null $pdo
     * @param string   $query
     * @return array<int, array<string, mixed>>
     */
    public static function search(?PDO $pdo = null, string $query = ''): array
    {
        $pdo         = $pdo ?? Database::getInstance()->getConnection();
        $searchQuery = '%' . $query . '%';

        $stmt = $pdo->prepare("
            SELECT p.*, u.name AS author_name
            FROM posts p
            JOIN users u ON u.id = p.author_id
            WHERE p.status = 'published'
              AND (p.title LIKE :q1 OR p.content LIKE :q2 OR p.excerpt LIKE :q3)
            ORDER BY p.published_at DESC
        ");
        $stmt->execute([
            ':q1' => $searchQuery,
            ':q2' => $searchQuery,
            ':q3' => $searchQuery,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna posts per estat.
     *
     * @param PDO|null $pdo
     * @param string   $status 'draft' | 'published' | 'archived'
     * @return array<int, array<string, mixed>>
     */
    public static function findByStatus(?PDO $pdo = null, string $status = 'published'): array
    {
        $pdo  = $pdo ?? Database::getInstance()->getConnection();
        $stmt = $pdo->prepare('SELECT * FROM posts WHERE status = :status ORDER BY created_at DESC');
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna els posts més vistos (publicats).
     *
     * @param PDO|null $pdo
     * @param int      $limit
     * @return array<int, array<string, mixed>>
     */
    public static function findMostViewed(?PDO $pdo = null, int $limit = 10): array
    {
        $pdo  = $pdo ?? Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM posts 
            WHERE status = 'published' 
            ORDER BY views_count DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna els posts més recents (publicats).
     *
     * @param PDO|null $pdo
     * @param int      $limit
     * @return array<int, array<string, mixed>>
     */
    public static function findRecent(?PDO $pdo = null, int $limit = 10): array
    {
        $pdo  = $pdo ?? Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM posts 
            WHERE status = 'published' 
            ORDER BY published_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}