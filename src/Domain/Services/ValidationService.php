<?php

/**
 * Validator
 *
 * Classe encarregada de validar dades sanejades
 * (normalment provinents de Sanitizer::clean($_POST)).
 *
 * Proporciona validació bàsica de camps, tipus, enums,
 * tags i dates amb control de longitud.
 */
class Validator
{
    /**
     * Valors permesos per enums
     * @var array
     */
    protected array $allowed = [
        'priority' => ['low', 'medium', 'high'],
        'state'    => ['pending', 'in-progress', 'blocked', 'completed']
    ];
     
    /** @var array Dades sanejades */
    private array $data;

    /** @var array Errors acumulats per camp */
    private array $errors = [];

    /**
     * @param array $data Dades sanejades
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Afegeix un error a un camp concret
     *
     * @param string $field Nom del camp
     * @param string $message Missatge d'error
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Comprova que un camp existeixi i no estigui buit
     *
     * @param string $field
     * @return self
     */
    public function required(string $field): self
    {
        if (
            !isset($this->data[$field]) || 
            $this->data[$field] === '' ||
            $this->data[$field] === null
        ) {
            $this->addError(
                $field, 
                "El camp $field és obligatori!"
            );
        }
        return $this;
    }

    /**
     * Valida un string amb longitud mínima i màxima
     *
     * @param string $field
     * @param int $min
     * @param int|null $max
     * @return self
     */
    public function string(string $field, int $min = 0, ?int $max = null): self
    {
        if (!isset($this->data[$field])) {
            return $this;
        }

        $value = (string)$this->data[$field];
        $length = mb_strlen($value);

        if ($length < $min) {
            $this->addError(
                $field, 
                "La descripció ha de tenir com a mínim {$min} caràcters."
            );
        }

        if ($max !== null && $length > $max) {
            $this->addError(
                $field, 
                "La descripció no pot superar els {$max} caràcters. Què vols escriure el quixot o que?"
            );
        }

        return $this;
    }

    /**
     * Comprova que un camp sigui numèric
     *
     * @param string $field
     * @return self
     */
    public function numeric(string $field): self 
    {
        if (!isset($this->data[$field]) || $this->data[$field] === '') {
            return $this;
        }

        if (!is_numeric($this->data[$field])) {
            $this->addError(
                $field,
                "$field ha de ser un valor numèric."
            );
        }

        return $this; 
    }

    /**
     * Valida una data amb format estricte
     *
     * @param string $field
     * @param string $format Format de date (per defecte: 'Y-m-d\TH:i')
     * @return self
     */
    public function date(string $field, string $format = 'Y-m-d\TH:i'): self
    {
        if (!isset($this->data[$field]) || $this->data[$field] === '') {
            return $this;
        }

        $date = \DateTime::createFromFormat($format, $this->data[$field]);

        if (!$date || $date->format($format) !== $this->data[$field]) {
            $this->addError(
                $field, 
                "Format de data invàlid"
            );
        }

        return $this;
    }

    /**
     * Valida que el valor estigui dins dels enums permesos
     *
     * @param string $field
     * @return self
     */
    public function enum(string $field): self
    {
        if (!isset($this->data[$field])) {
            return $this;
        }

        if (!in_array($this->data[$field], $this->allowed[$field], true)) {
            $this->addError(
                $field, 
                "Valor no permès."
            );
        }

        return $this;
    }

    /**
     * Valida un camp de tags (string separat per comes)
     *
     * @param string $field
     * @return self
     */
    public function tags(string $field): self
    {
        if (!isset($this->data[$field])) {
            return $this;
        }

        if (!is_string($this->data[$field])) {
            $this->addError(
                $field,
                "Format d'etiquetes invàlid"
            );
            return $this;
        }
        
        return $this;
    }
    
    /**
     * Indica si la validació ha passat sense errors
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Retorna els errors per camp
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }
}