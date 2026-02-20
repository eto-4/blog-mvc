<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Models\Admin;
use App\Http\Session\Session;
use App\Infrastructure\Security\Csrf;
use App\Infrastructure\Routing\Redirect;

/**
 * AdminController
 *
 * Gestiona el panell d'administració.
 *
 * Totes les accions requereixen que l'usuari tingui rol 'admin'.
 *
 * Rutes:
 *   GET  /admin                          → adminIndex()
 *   GET  /admin/users                    → adminUserList()
 *   POST /admin/users/{id}/delete        → adminDeleteUser(string $id)
 *   GET  /admin/posts                    → adminPosts()
 *   POST /admin/posts/{id}/status        → adminSwitchStatus(string $id)
 *   GET  /admin/audit                    → adminAuditLog()
 *   POST /admin/audit/{id}/restore       → adminRestore(string $id)
 *   POST /admin/audit/{id}/delete        → adminAuditDelete(string $id)
 */
class AdminController
{
    private Admin $adminModel;

    public function __construct()
    {
        $this->adminModel = new Admin();
    }

    // -------------------------------------------------------------------------
    // Helpers privats
    // -------------------------------------------------------------------------

    /**
     * Redirigeix si l'usuari no està autenticat o no és admin.
     */
    private function requireAdmin(): void
    {
        if (!Session::has('user_id') || Session::get('user_role') !== 'admin') {
            Redirect::withError('/', 'Accés no autoritzat.');
        }
    }

    /**
     * ID de l'admin autenticat.
     */
    private function adminId(): int
    {
        return (int) Session::get('user_id');
    }

    /**
     * Carrega header + vista + footer.
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
    // Dashboard
    // -------------------------------------------------------------------------

    /**
     * GET /admin
     * Panell principal amb estadístiques globals.
     */
    public function adminIndex(): void
    {
        $this->requireAdmin();

        $stats = $this->adminModel->getGlobalStats();

        $this->render('admin/index', [
            'stats' => $stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // Gestió d'usuaris
    // -------------------------------------------------------------------------

    /**
     * GET /admin/users
     * Llistat de tots els usuaris amb estadístiques.
     */
    public function adminUserList(): void
    {
        $this->requireAdmin();

        $users = $this->adminModel->getAllUsers();

        $this->render('admin/users', [
            'users' => $users,
        ]);
    }

    /**
     * POST /admin/users/{id}/delete
     * Elimina un usuari i guarda snapshot a audit_log.
     */
    public function adminDeleteUser(string $id): void
    {
        $this->requireAdmin();
        Csrf::validate();

        $userId = (int) $id;

        // Evitar que l'admin s'elimini a si mateix
        if ($userId === $this->adminId()) {
            Redirect::withError('/admin/users', 'No pots eliminar el teu propi compte.');
        }

        $this->adminModel->deleteUser($userId, $this->adminId());

        Redirect::withSuccess('/admin/users', 'Usuari eliminat correctament. Pots restaurar-lo des del historial.');
    }

    // -------------------------------------------------------------------------
    // Gestió de posts
    // -------------------------------------------------------------------------

    /**
     * GET /admin/posts
     * Llistat de tots els posts de tots els usuaris.
     */
    public function adminPosts(): void
    {
        $this->requireAdmin();

        $posts = $this->adminModel->getAllPosts();

        $this->render('admin/posts', [
            'posts' => $posts,
        ]);
    }

    /**
     * POST /admin/posts/{id}/status
     * Canvia l'estat d'un post.
     * Espera $_POST['status'] amb el nou estat.
     */
    public function adminSwitchStatus(string $id): void
    {
        $this->requireAdmin();
        Csrf::validate();

        $newStatus = $_POST['status'] ?? '';

        $this->adminModel->switchPostStatus((int) $id, $newStatus, $this->adminId());

        Redirect::withSuccess('/admin/posts', 'Estat del post actualitzat.');
    }

    // -------------------------------------------------------------------------
    // Audit Log
    // -------------------------------------------------------------------------

    /**
     * GET /admin/audit
     * Historial d'accions administratives no expirades.
     */
    public function adminAuditLog(): void
    {
        $this->requireAdmin();

        $entries = $this->adminModel->getAuditLog();

        $this->render('admin/audit', [
            'entries' => $entries,
        ]);
    }

    /**
     * POST /admin/audit/{id}/restore
     * Restaura l'entitat associada a una entrada del audit_log.
     */
    public function adminRestore(string $id): void
    {
        $this->requireAdmin();
        Csrf::validate();

        $success = $this->adminModel->restoreFromAudit((int) $id, $this->adminId());

        if ($success) {
            Redirect::withSuccess('/admin/audit', 'Entitat restaurada correctament.');
        } else {
            Redirect::withError('/admin/audit', 'No s\'ha pogut restaurar. Pot ser que l\'autor del post ja no existeixi o que l\'entrada hagi expirat.');
        }
    }

    /**
     * POST /admin/audit/{id}/delete
     * Elimina permanentment una entrada del audit_log.
     */
    public function adminAuditDelete(string $id): void
    {
        $this->requireAdmin();
        Csrf::validate();

        $this->adminModel->deleteAuditEntry((int) $id);

        Redirect::withSuccess('/admin/audit', 'Entrada eliminada permanentment del historial.');
    }
}