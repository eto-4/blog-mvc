<?php

class Validator
{
    protected array $allowed = [
        'priority' => ['low', 'medium', 'high'],
        'state'    => ['pending', 'in-progress', 'blocked', 'completed']
    ];
     
    private array $data;
    private array $errors = [];

    /**
     * @param array $data Dades ja sanejades (Sanitizer)
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Afegeix un error a un camp
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Comprova que un camp existeixi i no estigui buit
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
                "El camp és obligatori!"
            );
        }
        return $this;
    }

    /**
     * Valida un string amb longitud mínima i màxima
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
                "Ha de tenir com a minim {$min} caràcters."
            );
        }

        if ($max !== null && $length > $max) {
            $this->addError(
                $field, 
                "No pot superar els {$max} caràcters."
            );
        }

        return $this;
    }

    public function numeric(string $field): self 
    {
        if (!isset($this->data[$field]) || $this->data[$field] === '') {
            return $this;
        }

        if (!is_numeric($this->data[$field])) {
            $this->addError(
                $field,
                "Ha de ser un valor numèric."
            );
        }
    }

    /**
     * Valida una data amb format estricte
     */
    public function date(string $field, string $format = 'Y-m-d H:i:s'): self
    {
        if (!isset($this->data[$field]) || $this->data[$field] === '') {
            return $this;
        }

        $date = \DateTime::createFromFormat($format, $this->data[$field]);

        if (!$date || $date->format($format) !== $this->data[$field]) {
            $this->addError(
                $field, 
                "Format de date invàlid"
            );
        }

        return $this;
    }

    /**
     * Valida enums (valors tancats)
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
     * Valida arrays (ex: tags[])
     */
    public function array(string $field): self
    {
        if (!isset($this->data[$field])) {
            return $this;
        }

        if (!is_array($this->data[$field])) {
            $this->addError(
                $field,
                "Ha de ser una llista"
            );
        }

        return $this;
    }

    /**
     * Indica si la validació ha passat
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Retorna els errors per camp
     */
    public function errors(): array
    {
        return $this->errors;
    }
}