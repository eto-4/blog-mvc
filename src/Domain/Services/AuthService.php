<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Models\User;
use App\Http\Session\Session;
use App\Infrastructure\Database\DatabaseCore\Database;
use PDO;

/**
 * Servei d'autenticació d'usuaris.
 */
class AuthService
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    /**
     * Registra un nou usuari amb validació bàsica.
     *
     * @return array{success: bool, errors: array<string,string>}
     */
    public function register(string $name, string $email, string $password, string $passwordConfirm): array
    {
        $errors = [];

        $name = trim($name);
        $email = trim($email);

        if (mb_strlen($name) < 2 || mb_strlen($name) > 50) {
            $errors['name'] = 'El nom ha de tenir entre 2 i 50 caràcters.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'email no té un format vàlid.';
        }

        // Comprovar unicitat d'email
        $userModel = new User($this->pdo);
        if ($userModel->findByEmail($email)) {
            $errors['email'] = 'Ja existeix un usuari amb aquest email.';
        }

        if ($password !== $passwordConfirm) {
            $errors['password_confirmation'] = 'Les contrasenyes no coincideixen.';
        }

        if (!$this->isStrongPassword($password)) {
            $errors['password'] = 'La contrasenya ha de tenir mínim 8 caràcters, una majúscula, un número i un caràcter especial.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $userId = $userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);

        // Autologin després de registrar
        Session::set('user_id', $userId);
        Session::regenerate();

        return ['success' => true, 'errors' => []];
    }

    /**
     * Intenta iniciar sessió amb email i contrasenya.
     *
     * @return array{success: bool, errors: array<string,string>}
     */
    public function attemptLogin(string $email, string $password): array
    {
        $errors = [];
        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'email no té un format vàlid.';
            return ['success' => false, 'errors' => $errors];
        }

        $userModel = new User($this->pdo);
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $errors['email'] = 'Credencials incorrectes.';
            return ['success' => false, 'errors' => $errors];
        }

        Session::set('user_id', (int) $user['id']);
        Session::regenerate();

        // Actualitzar last_login_at
        $userModel->updateLastLogin((int) $user['id']);

        return ['success' => true, 'errors' => []];
    }

    public function logout(): void
    {
        Session::forget('user_id');
        Session::regenerate();
    }

    public function user(): ?array
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return null;
        }

        $userModel = new User($this->pdo);
        return $userModel->findById((int) $userId);
    }

    private function isStrongPassword(string $password): bool
    {
        if (strlen($password) < 8) {
            return false;
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        if (!preg_match('/[\W_]/', $password)) {
            return false;
        }

        return true;
    }
}

