<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Services\AuthService;
use App\Infrastructure\Security\Csrf;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function showLogin(): void
    {
        require APP_ROOT . '/src/Views/layouts/header.php';
        require APP_ROOT . '/src/Views/auth/login.php';
        require APP_ROOT . '/src/Views/layouts/footer.php';
    }

    public function login(): void
    {
        if (!Csrf::validate()) {
            http_response_code(403);
            echo 'Token CSRF invàlid.';
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $result = $this->authService->attemptLogin($email, $password);

        if (!$result['success']) {
            $errors = $result['errors'];
            require APP_ROOT . '/src/Views/layouts/header.php';
            require APP_ROOT . '/src/Views/auth/login.php';
            require APP_ROOT . '/src/Views/layouts/footer.php';
            return;
        }

        header('Location:' . BASE_PATH . '/');
        exit;
    }

    public function showRegister(): void
    {
        require APP_ROOT . '/src/Views/layouts/header.php';
        require APP_ROOT . '/src/Views/auth/register.php';
        require APP_ROOT . '/src/Views/layouts/footer.php';
    }

    public function register(): void
    {
        if (!Csrf::validate()) {
            http_response_code(403);
            echo 'Token CSRF invàlid.';
            return;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirmation'] ?? '';

        $result = $this->authService->register($name, $email, $password, $passwordConfirm);

        if (!$result['success']) {
            $errors = $result['errors'];
            require APP_ROOT . '/src/Views/layouts/header.php';
            require APP_ROOT . '/src/Views/auth/register.php';
            require APP_ROOT . '/src/Views/layouts/footer.php';
            return;
        }

        header('Location:' . BASE_PATH . '/');
        exit;
    }

    public function logout(): void
    {
        $this->authService->logout();
        header('Location:' . BASE_PATH . '/');
        exit;
    }
}

