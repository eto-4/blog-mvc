<?php

declare(strict_types=1);

namespace App\Domain\Slug;

/**
 * Value Object que representa un slug vàlid.
 *
 * Garanteix que el valor intern sempre compleix el format URL-friendly:
 * només minúscules, xifres i guions, sense guions al principi o al final.
 */
class Slug
{
    private string $value;

    /**
     * @param string $value  Slug ja generat i validat.
     * @throws \InvalidArgumentException Si el format no és vàlid.
     */
    public function __construct(string $value)
    {
        if (!self::isValid($value)) {
            throw new \InvalidArgumentException(
                "El slug \"{$value}\" no té un format vàlid."
            );
        }

        $this->value = $value;
    }

    /**
     * Retorna el valor del slug com a string.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Permet usar l'objecte directament com a string.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Comprova si un string té format de slug vàlid.
     * Format acceptat: minúscules, xifres i guions, sense guions als extrems.
     */
    public static function isValid(string $value): bool
    {
        return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value);
    }

    /**
     * Comprova igualtat entre dos slugs.
     */
    public function equals(Slug $other): bool
    {
        return $this->value === $other->value;
    }
}