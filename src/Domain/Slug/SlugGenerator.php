<?php

declare(strict_types=1);

namespace App\Domain\Slug;

use App\Infrastructure\Database\DatabaseCore\Database;
use PDO;

/**
 * Genera slugs URL-friendly a partir d'un títol.
 *
 * S'encarrega de:
 *  1. Convertir el títol a format slug (minúscules, sense accents, guions).
 *  2. Garantir la unicitat del slug a la taula `posts` de la BD.
 */
class SlugGenerator
{
    /**
     * Genera un slug únic a partir d'un títol.
     *
     * Si el slug base ja existeix a la BD, afegeix un sufix numèric
     * fins trobar un valor disponible: my-title, my-title-2, my-title-3...
     *
     * @param string   $title    Títol del post.
     * @param int|null $excludeId ID del post a excloure (útil en edicions).
     * @param PDO|null $pdo      Connexió PDO. Si és null, usa el Singleton.
     * @return string  Slug únic i vàlid.
     */
    public static function generate(string $title, ?int $excludeId = null, ?PDO $pdo = null): string
    {
        $pdo  = $pdo ?? Database::getInstance()->getConnection();
        $base = self::slugify($title);

        // Si el base està buit (títol sense caràcters vàlids), usar fallback
        if ($base === '') {
            $base = 'post-' . time();
        }

        $slug     = $base;
        $suffix   = 2;

        while (self::exists($pdo, $slug, $excludeId)) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * Converteix un string arbitrari a format slug.
     *
     * Passos:
     *  1. Convertir a minúscules.
     *  2. Substituir caràcters accentuats per equivalents ASCII.
     *  3. Eliminar qualsevol caràcter que no sigui alfanumèric o espai/guió.
     *  4. Substituir espais i guions múltiples per un sol guió.
     *  5. Eliminar guions als extrems.
     *
     * @param string $text
     * @return string
     */
    public static function slugify(string $text): string
    {
        // Minúscules
        $text = mb_strtolower($text, 'UTF-8');

        // Substituir caràcters accentuats i especials
        $replacements = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'ÿ' => 'y',
            'ñ' => 'n', 'ç' => 'c',
            'ł' => 'l', 'ß' => 'ss',
            '·' => '-', '/' => '-', '_' => '-', ',' => '-',
            ':' => '-', ';' => '-', '@' => '-',
        ];
        $text = strtr($text, $replacements);

        // Eliminar tot el que no sigui alfanumèric, espai o guió
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);

        // Substituir espais i guions múltiples per un sol guió
        $text = preg_replace('/[\s-]+/', '-', $text);

        // Eliminar guions als extrems
        return trim($text, '-');
    }

    /**
     * Comprova si un slug ja existeix a la taula posts.
     *
     * @param PDO      $pdo
     * @param string   $slug
     * @param int|null $excludeId  Post a ignorar en la comprovació (per a edicions).
     * @return bool
     */
    private static function exists(PDO $pdo, string $slug, ?int $excludeId): bool
    {
        if ($excludeId !== null) {
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) FROM posts WHERE slug = :slug AND id != :id'
            );
            $stmt->execute([':slug' => $slug, ':id' => $excludeId]);
        } else {
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) FROM posts WHERE slug = :slug'
            );
            $stmt->execute([':slug' => $slug]);
        }

        return (int) $stmt->fetchColumn() > 0;
    }
}