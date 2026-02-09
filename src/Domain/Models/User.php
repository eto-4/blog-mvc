<?php

declare(strict_types=1);

namespace App\Domain\Models;

use App\Infrastructure\Database\DatabaseCore\Database;
use PDO;

/**
 * Model senzill per a la taula "users".
 */
class User
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO users (name, email, password, bio, created_at, updated_at)
                VALUES (:name, :email, :password, :bio, NOW(), NOW())';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':bio' => $data['bio'] ?? null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT * FROM users WHERE email = :email LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM users WHERE id = :id LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function updateLastLogin(int $id): void
    {
        $sql = 'UPDATE users SET last_login_at = NOW(), updated_at = NOW() WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }
}

