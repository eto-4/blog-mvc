<?php

declare(strict_types=1);

namespace App\Domain\Models;

use App\Infrastructure\Database\DatabaseCore\Database;
use PDO;

/**
 * Model per a la taula "users".
 *
 * Gestiona totes les operacions CRUD i consultes relacionades amb els usuaris.
 */
class User
{
    private PDO $pdo;

    /**
     * @param PDO|null $pdo Connexió PDO. Si és null, s'utilitza el Singleton.
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    // -------------------------------------------------------------------------
    // Escriptura
    // -------------------------------------------------------------------------

    /**
     * Crea un nou usuari a la base de dades.
     *
     * @param array{name: string, email: string, password: string, bio?: string} $data
     * @return int ID de l'usuari creat
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO users (name, email, password, bio, created_at, updated_at)
                VALUES (:name, :email, :password, :bio, NOW(), NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name'     => $data['name'],
            ':email'    => $data['email'],
            ':password' => $data['password'],
            ':bio'      => $data['bio'] ?? null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Actualitza el perfil d'un usuari (nom i bio).
     *
     * @param int    $id
     * @param string $name
     * @param string $bio
     * @return void
     */
    public function updateProfile(int $id, string $name, string $bio): void
    {
        $sql = 'UPDATE users SET name = :name, bio = :bio, updated_at = NOW() WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'   => $id,
            ':name' => $name,
            ':bio'  => $bio,
        ]);
    }

    /**
     * Actualitza la contrasenya d'un usuari.
     *
     * @param int    $id
     * @param string $hashedPassword  Contrasenya ja hashejada amb password_hash()
     * @return void
     */
    public function updatePassword(int $id, string $hashedPassword): void
    {
        $sql = 'UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'       => $id,
            ':password' => $hashedPassword,
        ]);
    }

    /**
     * Actualitza l'avatar d'un usuari.
     *
     * @param int    $id
     * @param string $avatarPath  Ruta relativa del fitxer pujat
     * @return void
     */
    public function updateAvatar(int $id, string $avatarPath): void
    {
        $sql = 'UPDATE users SET avatar = :avatar, updated_at = NOW() WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'     => $id,
            ':avatar' => $avatarPath,
        ]);
    }

    /**
     * Actualitza el timestamp de last_login_at.
     *
     * @param int $id
     * @return void
     */
    public function updateLastLogin(int $id): void
    {
        $sql = 'UPDATE users SET last_login_at = NOW(), updated_at = NOW() WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    // -------------------------------------------------------------------------
    // Lectura
    // -------------------------------------------------------------------------

    /**
     * Busca un usuari per email.
     *
     * @param string $email
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        $sql  = 'SELECT * FROM users WHERE email = :email LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Busca un usuari per ID.
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql  = 'SELECT * FROM users WHERE id = :id LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Retorna tots els usuaris ordenats per data de creació.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM users ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna estadístiques d'un usuari:
     * total de posts, posts publicats i total de visualitzacions.
     *
     * @param int $userId
     * @return array{total_posts: int, published_posts: int, total_views: int}
     */
    public function getStats(int $userId): array
    {
        $sql = "SELECT 
                    COUNT(*) AS total_posts,
                    SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) AS published_posts,
                    COALESCE(SUM(views_count), 0) AS total_views
                FROM posts
                WHERE author_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total_posts'     => (int) ($row['total_posts']     ?? 0),
            'published_posts' => (int) ($row['published_posts'] ?? 0),
            'total_views'     => (int) ($row['total_views']     ?? 0),
        ];
    }
}