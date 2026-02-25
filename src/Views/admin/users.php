<?php
// views/admin/users.php
/** @var array $users */
?>

<div class="d-flex">

    <?php require APP_ROOT . '/src/Views/admin/sidebar.php'; ?>

    <main class="flex-grow-1 p-4 bg-light">

        <div class="d-flex align-items-center gap-3 mb-4">
            <h1 class="h4 fw-bold mb-0">Usuaris</h1>
            <span class="badge bg-primary"><?= count($users) ?></span>
        </div>

        <?php if (empty($users)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-people fs-1 d-block mb-3"></i>
                No hi ha usuaris registrats.
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Posts</th>
                                <th>Visualitzacions</th>
                                <th>Últim login</th>
                                <th>Registrat</th>
                                <th class="text-end">Accions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="text-muted small"><?= (int) $user['id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!empty($user['avatar'])): ?>
                                                <img src="<?= BASE_PATH ?>/storage/uploads/<?= htmlspecialchars($user['avatar'], ENT_QUOTES) ?>"
                                                     class="avatar-sm" alt="">
                                            <?php else: ?>
                                                <div class="avatar-placeholder">
                                                    <?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <span class="fw-semibold">
                                                <?= htmlspecialchars($user['name'], ENT_QUOTES) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-muted small"><?= htmlspecialchars($user['email'], ENT_QUOTES) ?></td>
                                    <td>
                                        <?php if ($user['role'] === 'admin'): ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-shield-lock me-1"></i>Admin
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Usuari</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= (int) $user['total_posts'] ?>
                                        <small class="text-muted">(<?= (int) $user['published_posts'] ?> pub.)</small>
                                    </td>
                                    <td>
                                        <i class="bi bi-eye text-muted me-1"></i><?= number_format((int) $user['total_views']) ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= $user['last_login_at'] ? date('d/m/Y H:i', strtotime($user['last_login_at'])) : '—' ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <?php if ((int) $user['id'] !== (int) ($_SESSION['user_id'] ?? 0)): ?>
                                                <form method="POST"
                                                      action="<?= BASE_PATH ?>/admin/users/<?= (int) $user['id'] ?>/delete"
                                                      onsubmit="return confirm('Segur que vols eliminar <?= htmlspecialchars($user['name'], ENT_QUOTES) ?>?')">
                                                    <?= \App\Infrastructure\Security\Csrf::field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted small fst-italic d-flex justify-content-center align-items-center p-2 px-2.5">Tú</span>
                                            <?php endif; ?>
                                            <a href="<?= BASE_PATH ?>/author/<?= (int) $user['id'] ?>"
                                               class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </main>
</div>