<?php

/**
 * Sanitizer
 *
 * Classe encarregada de netejar i normalitzar dades d'entrada
 * provinents de formularis HTML ($_POST, $_GET, etc.).
 *
 * No valida regles de negoci, només saneja valors per evitar
 * codi maliciós o caràcters innecessaris.
 */
class Sanitizer
{
    /**
     * Neteja un array de dades (normalment $_POST)
     *
     * @param array $data Array de dades d'entrada
     * @return array Array sanejat
     *
     * Exemple:
     * $_POST = ['title' => ' <b>Hola</b> ', 'tags' => ['php', '<script>'] ]
     * retorna ['title' => 'Hola', 'tags' => ['php']]
     */
    public static function clean(array $data): array
    {
        $cleaned = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $cleaned[$key] = self::cleanArray($value);
            } else {
                $cleaned[$key] = self::cleanValue($value);
            }
        }

        return $cleaned;
    }

    /**
     * Neteja un valor escalar (string)
     *
     * @param mixed $value Valor a netejar
     * @return mixed Valor sanejat
     *
     * Exemple: " <b>text</b> " -> "text"
     */
    public static function cleanValue($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $value = trim($value);      // Elimina espais al principi i final
        $value = strip_tags($value); // Elimina tags HTML

        return $value;
    }

    /**
     * Neteja un array de valors (ex: tags[])
     *
     * @param array $values Array de strings
     * @return array Array sanejat amb valors no buits
     *
     * Exemple: [' php ', '', '<b>tag</b>'] -> ['php','tag']
     */
    public static function cleanArray(array $values): array
    {
        $cleaned = [];

        foreach ($values as $value) {
            if (is_string($value)) {
                $value = trim($value);
                $value = strip_tags($value);

                if ($value !== '') {
                    $cleaned[] = $value;
                }
            }
        }

        return $cleaned;
    }

    /**
     * Converteix un valor a enter segur
     *
     * @param mixed $value
     * @return int|null Retorna enter o null si no és vàlid
     *
     * Exemple: "123" -> 123, "" -> null, "abc" -> null
     */
    public static function toInt($value): ?int
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Converteix un valor a float segur
     *
     * @param mixed $value
     * @return float|null Retorna float o null si no és vàlid
     *
     * Exemple: "3.14" -> 3.14, "" -> null, "abc" -> null
     */
    public static function toFloat($value): ?float
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    }   
}