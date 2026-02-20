<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Seeders;

use App\Domain\Slug\SlugGenerator;
use App\Infrastructure\Database\DatabaseCore\Database;
use Faker\Factory as FakerFactory;
use PDO;

require_once __DIR__ . '/../../../../vendor/autoload.php';

/**
 * Script per poblar la base de dades amb dades de prova.
 *
 * Execució (des de l'arrel del projecte):
 * php src/Infrastructure/Database/Seeders/DatabaseSeeder.php
 */

$pdo   = Database::getInstance()->getConnection();
$faker = FakerFactory::create('ca_ES'); // Català per tenir noms i textos més realistes

// -------------------------------------------------------------------------
// Usuaris de prova
// -------------------------------------------------------------------------
$users = [
    [
        'name'     => 'Admin User',
        'email'    => 'admin@blog.com',
        'password' => password_hash('Admin123!', PASSWORD_BCRYPT),
        'role'     => 'admin',
    ],
    [
        'name'     => 'John Doe',
        'email'    => 'john@blog.com',
        'password' => password_hash('User123!', PASSWORD_BCRYPT),
        'role'     => 'user',
    ],
    [
        'name'     => 'Jane Smith',
        'email'    => 'jane@blog.com',
        'password' => password_hash('User123!', PASSWORD_BCRYPT),
        'role'     => 'user',
    ],
    [
        'name'     => 'Guest User',
        'email'    => 'guest@blog.com',
        'password' => password_hash('Guest123!', PASSWORD_BCRYPT),
        'role'     => 'user',
    ],
];

// Afegir usuaris extra fins a tenir almenys 5
while (count($users) < 5) {
    $users[] = [
        'name'     => $faker->name(),
        'email'    => $faker->unique()->safeEmail(),
        'password' => password_hash('User123!', PASSWORD_BCRYPT),
        'role'     => 'user',
    ];
}

// -------------------------------------------------------------------------
// Netejar taules desactivant foreign keys temporalment
// -------------------------------------------------------------------------
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('DELETE FROM audit_log');
$pdo->exec('DELETE FROM posts');
$pdo->exec('DELETE FROM users');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

// -------------------------------------------------------------------------
// Inserir usuaris
// -------------------------------------------------------------------------
$userStmt = $pdo->prepare('
    INSERT INTO users (name, email, password, bio, role, created_at, updated_at)
    VALUES (:name, :email, :password, :bio, :role, NOW(), NOW())
');

foreach ($users as $user) {
    $userStmt->execute([
        ':name'     => $user['name'],
        ':email'    => $user['email'],
        ':password' => $user['password'],
        ':bio'      => $faker->sentence(10),
        ':role'     => $user['role'],
    ]);
}

// Recuperar IDs per assignar posts
$userIds = $pdo->query('SELECT id FROM users')->fetchAll(PDO::FETCH_COLUMN);

// -------------------------------------------------------------------------
// Inserir posts
// -------------------------------------------------------------------------
$postStmt = $pdo->prepare('
    INSERT INTO posts
        (title, slug, content, excerpt, featured_image,
         author_id, status, views_count, published_at, created_at, updated_at)
    VALUES
        (:title, :slug, :content, :excerpt, :featured_image,
         :author_id, :status, :views_count, :published_at, :created_at, :updated_at)
');

$totalPosts    = 20; // Una mica més per tenir varietat
$usedSlugs     = [];

for ($i = 0; $i < $totalPosts; $i++) {
    $title = $faker->sentence(mt_rand(4, 8));

    // Generar slug únic sense consulta a BD (ja hem esborrat tot)
    $baseSlug = SlugGenerator::slugify($title);
    $slug     = $baseSlug;
    $suffix   = 2;
    while (in_array($slug, $usedSlugs, true)) {
        $slug = $baseSlug . '-' . $suffix++;
    }
    $usedSlugs[] = $slug;

    $statusOptions = ['draft', 'published', 'published', 'published', 'archived'];
    $status        = $statusOptions[array_rand($statusOptions)]; // Més probabilitat de published

    $publishedAt = null;
    if ($status === 'published') {
        $publishedAt = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s');
    }

    $createdAt = $faker->dateTimeBetween('-2 years', '-1 day')->format('Y-m-d H:i:s');
    $updatedAt = $faker->dateTimeBetween($createdAt, 'now')->format('Y-m-d H:i:s');

    $content = '<p>' . implode('</p><p>', $faker->paragraphs(mt_rand(3, 8))) . '</p>';
    $excerpt = $faker->text(150);

    $postStmt->execute([
        ':title'          => $title,
        ':slug'           => $slug,
        ':content'        => $content,
        ':excerpt'        => $excerpt,
        ':featured_image' => null,
        ':author_id'      => $userIds[array_rand($userIds)],
        ':status'         => $status,
        ':views_count'    => mt_rand(0, 500),
        ':published_at'   => $publishedAt,
        ':created_at'     => $createdAt,
        ':updated_at'     => $updatedAt,
    ]);
}

echo "Base de dades poblada correctament:" . PHP_EOL;
echo " - " . count($users) . " usuaris (1 admin + " . (count($users) - 1) . " users)" . PHP_EOL;
echo " - {$totalPosts} posts amb estats variats" . PHP_EOL;