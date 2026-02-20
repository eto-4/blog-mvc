<?php

declare(strict_types=1);

namespace App\Domain\Models;

use App\Infrastructure\Database\DatabaseCore\Database;
use PDO;

/**
 * Model Admin
 *
 * Gestiona les operacions administratives:
 * estadístiques globals, gestió d'usuaris i posts,
 * i el registre d'auditoria (audit_log).
 */
class Admin
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    // -------------------------------------------------------------------------
    // Estadístiques globals (dashboard)
    // -------------------------------------------------------------------------

    /**
     * Retorna estadístiques generals de l'aplicació.
     *
     * @return array{
     *   total_users: int,
     *   total_posts: int,
     *   published_posts: int,
     *   draft_posts: int,
     *   archived_posts: int,
     *   total_views: int,
     *   new_users_30d: int,
     *   new_posts_30d: int
     * }
     */
    public function getGlobalStats(): array
    {
        $stats = [];

        // Usuaris
        $row = $this->pdo->query("
            SELECT
                COUNT(*) AS total_users,
                SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS new_users_30d
            FROM users
        ")->fetch(PDO::FETCH_ASSOC);

        $stats['total_users']    = (int) $row['total_users'];
        $stats['new_users_30d']  = (int) $row['new_users_30d'];

        // Posts
        $row = $this->pdo->query("
            SELECT
                COUNT(*) AS total_posts,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) AS published_posts,
                SUM(CASE WHEN status = 'draft'     THEN 1 ELSE 0 END) AS draft_posts,
                SUM(CASE WHEN status = 'archived'  THEN 1 ELSE 0 END) AS archived_posts,
                COALESCE(SUM(views_count), 0)                          AS total_views,
                SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS new_posts_30d
            FROM posts
        ")->fetch(PDO::FETCH_ASSOC);

        $stats['total_posts']     = (int) $row['total_posts'];
        $stats['published_posts'] = (int) $row['published_posts'];
        $stats['draft_posts']     = (int) $row['draft_posts'];
        $stats['archived_posts']  = (int) $row['archived_posts'];
        $stats['total_views']     = (int) $row['total_views'];
        $stats['new_posts_30d']   = (int) $row['new_posts_30d'];

        return $stats;
    }

    // -------------------------------------------------------------------------
    // Gestió d'usuaris
    // -------------------------------------------------------------------------

    /**
     * Retorna tots els usuaris amb el recompte de posts de cadascun.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllUsers(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                u.*,
                COUNT(p.id)                                               AS total_posts,
                SUM(CASE WHEN p.status = 'published' THEN 1 ELSE 0 END)  AS published_posts,
                COALESCE(SUM(p.views_count), 0)                           AS total_views
            FROM users u
            LEFT JOIN posts p ON p.author_id = u.id
            GROUP BY u.id
            ORDER BY u.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina un usuari i guarda un snapshot a audit_log.
     *
     * @param int $userId       ID de l'usuari a eliminar.
     * @param int $performedBy  ID de l'admin que fa l'acció.
     * @return void
     */
    public function deleteUser(int $userId, int $performedBy): void
    {
        // Obtenir dades completes abans d'eliminar
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $userId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            return;
        }

        // Guardar snapshot a audit_log
        $this->logAction('delete_user', 'user', $userId, $userData, $performedBy);

        // Eliminar (CASCADE eliminarà els posts associats)
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute([':id' => $userId]);
    }

    // -------------------------------------------------------------------------
    // Gestió de posts (admin)
    // -------------------------------------------------------------------------

    /**
     * Retorna tots els posts de tots els usuaris amb dades de l'autor.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllPosts(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                p.*,
                u.name  AS author_name,
                u.email AS author_email
            FROM posts p
            JOIN users u ON u.id = p.author_id
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Canvia l'estat d'un post i guarda l'acció a audit_log.
     *
     * @param int    $postId      ID del post.
     * @param string $newStatus   'draft' | 'published' | 'archived'
     * @param int    $performedBy ID de l'admin que fa l'acció.
     * @return void
     */
    public function switchPostStatus(int $postId, string $newStatus, int $performedBy): void
    {
        $allowed = ['draft', 'published', 'archived'];
        if (!in_array($newStatus, $allowed, true)) {
            return;
        }

        // Snapshot abans del canvi
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE id = :id');
        $stmt->execute([':id' => $postId]);
        $postData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$postData) {
            return;
        }

        $this->logAction('switch_post_status', 'post', $postId, $postData, $performedBy);

        $publishedAt = $newStatus === 'published' && $postData['published_at'] === null
            ? date('Y-m-d H:i:s')
            : $postData['published_at'];

        $stmt = $this->pdo->prepare("
            UPDATE posts
            SET status = :status, published_at = :published_at, updated_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            ':status'       => $newStatus,
            ':published_at' => $publishedAt,
            ':id'           => $postId,
        ]);
    }

    /**
     * Elimina un post i guarda un snapshot a audit_log.
     *
     * @param int $postId      ID del post a eliminar.
     * @param int $performedBy ID de l'admin que fa l'acció.
     * @return void
     */
    public function deletePost(int $postId, int $performedBy): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE id = :id');
        $stmt->execute([':id' => $postId]);
        $postData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$postData) {
            return;
        }

        $this->logAction('delete_post', 'post', $postId, $postData, $performedBy);

        $stmt = $this->pdo->prepare('DELETE FROM posts WHERE id = :id');
        $stmt->execute([':id' => $postId]);
    }

    // -------------------------------------------------------------------------
    // Audit Log
    // -------------------------------------------------------------------------

    /**
     * Retorna totes les entrades del audit_log no expirades,
     * amb el nom de l'admin que va fer l'acció.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAuditLog(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                al.*,
                u.name AS admin_name
            FROM audit_log al
            JOIN users u ON u.id = al.performed_by
            WHERE al.expires_at > NOW()
            ORDER BY al.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna una entrada del audit_log per ID.
     *
     * @param int $auditId
     * @return array<string, mixed>|null
     */
    public function getAuditEntry(int $auditId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM audit_log WHERE id = :id');
        $stmt->execute([':id' => $auditId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Restaura una entitat eliminada a partir del snapshot del audit_log.
     * Suporta: 'user', 'post'.
     *
     * @param int $auditId     ID de l'entrada al audit_log.
     * @param int $performedBy ID de l'admin que fa la restauració.
     * @return bool True si s'ha restaurat, false si no s'ha trobat o no és suportada.
     */
    public function restoreFromAudit(int $auditId, int $performedBy): bool
    {
        $entry = $this->getAuditEntry($auditId);

        if (!$entry) {
            return false;
        }

        $data = json_decode($entry['entity_data'], true);

        switch ($entry['entity_type']) {
            case 'user':
                return $this->restoreUser($data, $auditId, $performedBy);

            case 'post':
                return $this->restorePost($data, $auditId, $performedBy);

            default:
                return false;
        }
    }

    /**
     * Elimina permanentment una entrada del audit_log.
     *
     * @param int $auditId
     * @return void
     */
    public function deleteAuditEntry(int $auditId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM audit_log WHERE id = :id');
        $stmt->execute([':id' => $auditId]);
    }

    /**
     * Elimina totes les entrades expirades del audit_log.
     * Útil per executar periòdicament (cron job).
     *
     * @return int Nombre d'entrades eliminades.
     */
    public function purgeExpired(): int
    {
        $stmt = $this->pdo->query("DELETE FROM audit_log WHERE expires_at <= NOW()");
        return $stmt->rowCount();
    }

    // -------------------------------------------------------------------------
    // Helpers privats
    // -------------------------------------------------------------------------

    /**
     * Insereix una entrada al audit_log.
     *
     * @param string               $action
     * @param string               $entityType
     * @param int                  $entityId
     * @param array<string, mixed> $entityData  Snapshot de l'entitat.
     * @param int                  $performedBy
     * @return void
     */
    private function logAction(
        string $action,
        string $entityType,
        int    $entityId,
        array  $entityData,
        int    $performedBy
    ): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO audit_log (action, entity_type, entity_id, entity_data, performed_by)
            VALUES (:action, :entity_type, :entity_id, :entity_data, :performed_by)
        ");
        $stmt->execute([
            ':action'      => $action,
            ':entity_type' => $entityType,
            ':entity_id'   => $entityId,
            ':entity_data' => json_encode($entityData, JSON_UNESCAPED_UNICODE),
            ':performed_by'=> $performedBy,
        ]);
    }

    /**
     * Restaura un usuari a partir d'un snapshot.
     * Si l'email ja existeix, afegeix un sufix per evitar conflictes.
     */
    private function restoreUser(array $data, int $auditId, int $performedBy): bool
    {
        // Comprovar si l'email ja existeix
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $stmt->execute([':email' => $data['email']]);
        if ((int) $stmt->fetchColumn() > 0) {
            $data['email'] = 'restored_' . time() . '_' . $data['email'];
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO users (id, name, email, password, avatar, bio, role, created_at, updated_at)
            VALUES (:id, :name, :email, :password, :avatar, :bio, :role, :created_at, NOW())
        ");
        $stmt->execute([
            ':id'         => $data['id'],
            ':name'       => $data['name'],
            ':email'      => $data['email'],
            ':password'   => $data['password'],
            ':avatar'     => $data['avatar'],
            ':bio'        => $data['bio'],
            ':role'       => $data['role'] ?? 'user',
            ':created_at' => $data['created_at'],
        ]);

        // Registrar la restauració al audit_log
        $this->logAction('restore_user', 'user', (int) $data['id'], $data, $performedBy);

        return true;
    }

    /**
     * Restaura un post a partir d'un snapshot.
     * Si l'autor ja no existeix, no es pot restaurar.
     */
    private function restorePost(array $data, int $auditId, int $performedBy): bool
    {
        // Comprovar que l'autor encara existeix
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE id = :id');
        $stmt->execute([':id' => $data['author_id']]);
        if ((int) $stmt->fetchColumn() === 0) {
            return false;
        }

        // Comprovar conflicte de slug
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM posts WHERE slug = :slug');
        $stmt->execute([':slug' => $data['slug']]);
        if ((int) $stmt->fetchColumn() > 0) {
            $data['slug'] = $data['slug'] . '-restored-' . time();
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO posts
                (id, title, slug, content, excerpt, featured_image,
                 author_id, status, views_count, published_at, created_at, updated_at)
            VALUES
                (:id, :title, :slug, :content, :excerpt, :featured_image,
                 :author_id, :status, :views_count, :published_at, :created_at, NOW())
        ");
        $stmt->execute([
            ':id'            => $data['id'],
            ':title'         => $data['title'],
            ':slug'          => $data['slug'],
            ':content'       => $data['content'],
            ':excerpt'       => $data['excerpt'],
            ':featured_image'=> $data['featured_image'],
            ':author_id'     => $data['author_id'],
            ':status'        => 'draft', // sempre restaurem com a draft per seguretat
            ':views_count'   => $data['views_count'],
            ':published_at'  => $data['published_at'],
            ':created_at'    => $data['created_at'],
        ]);

        $this->logAction('restore_post', 'post', (int) $data['id'], $data, $performedBy);

        return true;
    }
}