<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Models\User;
use App\Domain\Services\AuthService;
use App\Http\Session\Session;
use App\Infrastructure\Security\Csrf;
use App\Infrastructure\Routing\Redirect;

/**
 * UserController
 *
 * Gestiona les accions relacionades amb el perfil de l'usuari autenticat.
 *
 * Rutes:
 *   GET  /profile           → userProfile()
 *   GET  /profile/edit      → userEditProfile()
 *   POST /profile/update    → update()
 *   POST /profile/avatar    → avatar()
 */
class UserController
{
    private User        $userModel;
    private AuthService $authService;

    public function __construct()
    {
        $this->userModel   = new User();
        $this->authService = new AuthService();
    }

    // -------------------------------------------------------------------------
    // Helpers privats
    // -------------------------------------------------------------------------

    /**
     * Redirigeix a login si l'usuari no està autenticat.
     */
    private function requireAuth(): void
    {
        if (!Session::has('user_id')) {
            Redirect::to('/login');
        }
    }

    /**
     * Retorna l'usuari autenticat o redirigeix.
     *
     * @return array<string, mixed>
     */
    private function currentUser(): array
    {
        $user = $this->userModel->findById((int) Session::get('user_id'));

        if (!$user) {
            // Sessió invàlida, tancar i redirigir
            Session::forget('user_id');
            Redirect::to('/login');
        }

        return $user;
    }

    /**
     * Carrega header + vista + footer passant variables a la vista.
     *
     * @param string               $view
     * @param array<string, mixed> $data
     */
    private function render(string $view, array $data = []): void
    {
        extract($data);
        require APP_ROOT . '/src/Views/layouts/header.php';
        require APP_ROOT . "/src/Views/{$view}.php";
        require APP_ROOT . '/src/Views/layouts/footer.php';
    }

    // -------------------------------------------------------------------------
    // Accions de perfil
    // -------------------------------------------------------------------------

    /**
     * GET /profile
     * Mostra el perfil de l'usuari autenticat amb estadístiques.
     */
    public function userProfile(): void
    {
        $this->requireAuth();

        $user  = $this->currentUser();
        $stats = $this->userModel->getStats((int) $user['id']);

        $this->render('user/profile', [
            'user'  => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * GET /profile/edit
     * Mostra el formulari d'edició del perfil.
     */
    public function userEditProfile(): void
    {
        $this->requireAuth();

        $user = $this->currentUser();

        $this->render('user/edit', [
            'user'      => $user,
            'csrfToken' => Csrf::generate(),
        ]);
    }

    /**
     * POST /profile/update
     * Processa el formulari d'edició del perfil.
     * Gestiona nom, bio i canvi opcional de contrasenya.
     */
    public function update(): void
    {
        $this->requireAuth();
        Csrf::validate();

        $user = $this->currentUser();
        $id   = (int) $user['id'];

        $name            = trim($_POST['name']             ?? '');
        $bio             = trim($_POST['bio']              ?? '');
        $password        = $_POST['password']              ?? '';
        $passwordConfirm = $_POST['password_confirmation'] ?? '';

        $errors = $this->validateProfile($name, $password, $passwordConfirm);

        if (!empty($errors)) {
            $this->render('user/edit', [
                'user'      => $user,
                'errors'    => $errors,
                'old'       => $_POST,
                'csrfToken' => Csrf::generate(),
            ]);
            return;
        }

        // Actualitzar nom i bio
        $this->userModel->updateProfile($id, $name, $bio);

        // Canviar contrasenya només si s'ha introduït
        if ($password !== '') {
            $this->userModel->updatePassword($id, password_hash($password, PASSWORD_BCRYPT));
        }

        Redirect::withSuccess('/profile', 'Perfil actualitzat correctament.');
    }

    /**
     * POST /profile/avatar
     * Processa la pujada d'un nou avatar.
     */
    public function avatar(): void
    {
        $this->requireAuth();
        Csrf::validate();

        $user = $this->currentUser();
        $id   = (int) $user['id'];

        // Comprovar que s'ha enviat un fitxer
        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            Redirect::withError('/profile/edit', 'No s\'ha pogut pujar l\'avatar. Torna-ho a intentar.');
        }

        $file     = $_FILES['avatar'];
        $maxSize  = 2 * 1024 * 1024; // 2 MB
        $allowed  = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        $mimeType = mime_content_type($file['tmp_name']);

        // Validacions del fitxer
        if ($file['size'] > $maxSize) {
            Redirect::withError('/profile/edit', 'L\'avatar no pot superar els 2 MB.');
        }

        if (!in_array($mimeType, $allowed, true)) {
            Redirect::withError('/profile/edit', 'Format no permès. Utilitza JPG, PNG, JPEG o WEBP.');
        }

        // Generar nom únic i moure el fitxer
        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $id . '_' . time() . '.' . $ext;
        $destDir  = APP_ROOT . '/storage/uploads/avatars/';
        $destPath = $destDir . $filename;

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        // Eliminar avatar anterior si existeix i no és el per defecte
        if (!empty($user['avatar']) && file_exists(APP_ROOT . '/storage/uploads/' . $user['avatar'])) {
            unlink(APP_ROOT . '/storage/uploads/' . $user['avatar']);
        }

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            Redirect::withError('/profile/edit', 'Error en moure el fitxer. Torna-ho a intentar.');
        }

        $this->userModel->updateAvatar($id, 'avatars/' . $filename);

        Redirect::withSuccess('/profile', 'Avatar actualitzat correctament.');
    }

    // -------------------------------------------------------------------------
    // Helpers de validació privats
    // -------------------------------------------------------------------------

    /**
     * Valida els camps del formulari d'edició de perfil.
     *
     * @return array<string, string> Errors indexats per camp
     */
    private function validateProfile(string $name, string $password, string $passwordConfirm): array
    {
        $errors = [];

        if (mb_strlen($name) < 2) {
            $errors['name'] = 'El nom ha de tenir mínim 2 caràcters.';
        } elseif (mb_strlen($name) > 50) {
            $errors['name'] = 'El nom no pot superar els 50 caràcters.';
        }

        // Validar contrasenya només si s'ha introduït alguna cosa
        if ($password !== '') {
            if ($password !== $passwordConfirm) {
                $errors['password_confirmation'] = 'Les contrasenyes no coincideixen.';
            } elseif (!$this->isStrongPassword($password)) {
                $errors['password'] = 'La contrasenya ha de tenir mínim 8 caràcters, una majúscula, un número i un caràcter especial.';
            }
        }

        return $errors;
    }

    /**
     * Comprova que la contrasenya compleix els requisits de seguretat.
     * Mateixa lògica que AuthService per consistència. (Està duplicat expressament).
     * No es un bug, s'ha implementat de manera duplicada per evitar errors 
     * d'arquitectura i repartició de responsabilitats en el codi. 
     * (Es pot extreure a una classe d'utilitat petita, pero no val la pena).
     */
    private function isStrongPassword(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[0-9]/', $password)
            && preg_match('/[\W_]/', $password);
    }
}