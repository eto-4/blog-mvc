<?php

/**
 * Model de Post
 */
class Post
{
    private PDO $pdo;
    public $id;
    public $title;
    public $slug;
    public $content;
    public $excerpt;
    public $featured_image;
    public $author_id;
    public $status;
    public $views_count;
    public $published_at;
    public $created_at;
    public $updated_at;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Carrega un post per ID
     */
    public function load(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
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
     * Desa un nou post
     */
    public function save(): void
    {
        $sql = "INSERT INTO posts (title, slug, content, excerpt, featured_image, author_id, status, views_count, published_at) 
                VALUES (:title, :slug, :content, :excerpt, :featured_image, :author_id, :status, :views_count, :published_at)";
        
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':excerpt', $this->excerpt);
        $stmt->bindParam(':featured_image', $this->featured_image);
        $stmt->bindParam(':author_id', $this->author_id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':views_count', $this->views_count, PDO::PARAM_INT);
        $stmt->bindParam(':published_at', $this->published_at);
        
        $stmt->execute();
        
        $this->id = $this->pdo->lastInsertId();
    }

    /**
     * Actualitza un post existent
     */
    public function update(): void
    {
        $sql = "UPDATE posts SET 
                title = :title,
                slug = :slug,
                content = :content,
                excerpt = :excerpt,
                featured_image = :featured_image,
                author_id = :author_id,
                status = :status,
                views_count = :views_count,
                published_at = :published_at,
                updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':excerpt', $this->excerpt);
        $stmt->bindParam(':featured_image', $this->featured_image);
        $stmt->bindParam(':author_id', $this->author_id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':views_count', $this->views_count, PDO::PARAM_INT);
        $stmt->bindParam(':published_at', $this->published_at);
        
        $stmt->execute();
    }

    /**
     * Elimina el post
     */
    public function delete(): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = :id");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Incrementa el comptador de visualitzacions
     */
    public function incrementViews(): void
    {
        $stmt = $this->pdo->prepare("UPDATE posts SET views_count = views_count + 1 WHERE id = :id");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        
        $this->views_count++;
    }

    /**
     * Retorna tots els posts
     */
    public static function findAll(PDO $pdo): array
    {
        $stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca posts per paraules clau
     */
    public static function search(PDO $pdo, string $query): array
    {
        $searchQuery = "%$query%";
        $stmt = $pdo->prepare("
            SELECT * FROM posts 
            WHERE title LIKE :query 
               OR content LIKE :query 
               OR excerpt LIKE :query
            ORDER BY created_at DESC
        ");
        $stmt->bindParam(':query', $searchQuery);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna posts per estat
     */
    public static function findByStatus(PDO $pdo, string $status): array
    {
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE status = :status ORDER BY created_at DESC");
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna posts per autor
     */
    public static function findByAuthor(PDO $pdo, int $authorId): array
    {
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE author_id = :author_id ORDER BY created_at DESC");
        $stmt->bindParam(':author_id', $authorId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna els posts més vistos
     */
    public static function findMostViewed(PDO $pdo, int $limit = 10): array
    {
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE status = 'published' ORDER BY views_count DESC LIMIT :limit");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna els posts recents
     */
    public static function findRecent(PDO $pdo, int $limit = 10): array
    {
        $stmt = $pdo->prepare("
            SELECT * FROM posts 
            WHERE status = 'published' 
            ORDER BY published_at DESC 
            LIMIT :limit
        ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}