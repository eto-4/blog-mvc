<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Seeders;

use App\Infrastructure\Database\DatabaseCore\Database;
use Faker\Factory as FakerFactory;
use PDO;

require_once __DIR__ . '/../../../../vendor/autoload.php';

/**
 * Script senzill per poblar la base de dades amb dades de prova.
 *
 * Execució (des de l'arrel del projecte):
 * php src/Infrastructure/Database/Seeders/DatabaseSeeder.php
 */

$pdo = Database::getInstance()->getConnection();

$faker = FakerFactory::create();

// Usuaris de prova mínims
$users = [
    [
        'name' => 'Admin User',
        'email' => 'admin@blog.com',
        'password' => password_hash('Admin123!', PASSWORD_BCRYPT),
    ],
    [
        'name' => 'John Doe',
        'email' => 'john@blog.com',
        'password' => password_hash('User123!', PASSWORD_BCRYPT),
    ],
    [
        'name' => 'Jane Smith',
        'email' => 'jane@blog.com',
        'password' => password_hash('User123!', PASSWORD_BCRYPT),
    ],
    [
        'name' => 'Guest User',
        'email' => 'guest@blog.com',
        'password' => password_hash('Guest123!', PASSWORD_BCRYPT),
    ],
];

// Afegir usuaris extra fins a tenir almenys 5
while (count($users) < 5) {
    $users[] = [
        'name' => $faker->name(),
        'email' => $faker->unique()->safeEmail(),
        'password' => password_hash('User123!', PASSWORD_BCRYPT),
    ];
}

// Netejar taules (en entorn de desenvolupament)
$pdo->exec('DELETE FROM posts');
$pdo->exec('DELETE FROM users');

$insertUserSql = 'INSERT INTO users (name, email, password, bio, created_at, updated_at)
                  VALUES (:name, :email, :password, :bio, NOW(), NOW())';
$userStmt = $pdo->prepare($insertUserSql);

foreach ($users as $user) {
    $userStmt->execute([
        ':name' => $user['name'],
        ':email' => $user['email'],
        ':password' => $user['password'],
        ':bio' => $faker->sentence(10),
    ]);
}

// Recuperar tots els usuaris per assignar posts
$userIds = $pdo->query('SELECT id FROM users')->fetchAll(PDO::FETCH_COLUMN);

$insertPostSql = 'INSERT INTO posts 
    (title, slug, content, excerpt, featured_image, author_id, status, views_count, published_at, created_at, updated_at)
    VALUES (:title, :slug, :content, :excerpt, :featured_image, :author_id, :status, :views_count, :published_at, :created_at, :updated_at)';
$postStmt = $pdo->prepare($insertPostSql);

$totalPosts = 15;

for ($i = 0; $i < $totalPosts; $i++) {
    $title = $faker->sentence(6);
    $slugBase = strtolower(trim(preg_replace('/[^a-z0-9-]+/i', '-', $title), '-'));
    $slug = $slugBase . '-' . $i;

    $statusOptions = ['draft', 'published', 'archived'];
    $status = $statusOptions[array_rand($statusOptions)];

    $publishedAt = null;
    if ($status === 'published') {
        $publishedAt = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s');
    }

    $createdAt = $faker->dateTimeBetween('-2 years', '-1 day')->format('Y-m-d H:i:s');
    $updatedAt = $faker->dateTimeBetween($createdAt, 'now')->format('Y-m-d H:i:s');

    $content = '<p>' . implode('</p><p>', $faker->paragraphs(mt_rand(3, 8))) . '</p>';

    $postStmt->execute([
        ':title' => $title,
        ':slug' => $slug,
        ':content' => $content,
        ':excerpt' => $faker->text(150),
        ':featured_image' => null,
        ':author_id' => $userIds[array_rand($userIds)],
        ':status' => $status,
        ':views_count' => mt_rand(0, 500),
        ':published_at' => $publishedAt,
        ':created_at' => $createdAt,
        ':updated_at' => $updatedAt,
    ]);
}

echo "Base de dades poblada correctament amb usuaris i posts de prova." . PHP_EOL;

