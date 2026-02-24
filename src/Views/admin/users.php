<?php
// views/admin/users.php

/** @var array $users */
?>

<section class="admin-layout">

    <?php require APP_ROOT . '/src/Views/admin/sidebar.php'; ?>

    <main class="admin-main">

        <div class="admin-page-header">
            <h1 class="admin-page-title">Usuaris</h1>
            <span class="admin-count"><?= count($users) ?> usuaris</span>
        </div>

        <?php if (empty($users)): ?>
            <p class="empty-state">No hi ha usuaris registrats.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Posts</th>
                            <th>Visualitzacions</th>
                            <th>Últim login</th>
                            <th>Registrat</th>
                            <th>Accions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= (int) $user['id'] ?></td>
                                <td class="admin-user-name">
                                    <?php if (!empty($user['avatar'])): ?>
                                        <img src="<?= BASE_PATH ?>/storage/uploads/<?= htmlspecialchars($user['avatar'], ENT_QUOTES) ?>"
                                             class="admin-avatar-sm" alt="">
                                    <?php else: ?>
                                        <span class="admin-avatar-sm-placeholder">
                                            <?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($user['name'], ENT_QUOTES) ?>
                                </td>
                                <td><?= htmlspecialchars($user['email'], ENT_QUOTES) ?></td>
                                <td>
                                    <span class="role-badge role-<?= $user['role'] ?? 'user' ?>">
                                        <?= $user['role'] === 'admin' ? 'Admin' : 'Usuari' ?>
                                    </span>
                                </td>
                                <td><?= (int) $user['total_posts'] ?> <small>(<?= (int) $user['published_posts'] ?> pub.)</small></td>
                                <td>👁 <?= number_format((int) $user['total_views']) ?></td>
                                <td><?= $user['last_login_at'] ? date('d/m/Y H:i', strtotime($user['last_login_at'])) : '—' ?></td>
                                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                <td class="admin-actions">
                                    <a href="<?= BASE_PATH ?>/author/<?= (int) $user['id'] ?>"
                                       class="btn btn-sm btn-primary" target="_blank">Veure</a>

                                    <?php if ((int) $user['id'] !== (int) ($_SESSION['user_id'] ?? 0)): ?>
                                        <form method="POST"
                                              action="<?= BASE_PATH ?>/admin/users/<?= (int) $user['id'] ?>/delete"
                                              style="display:inline"
                                              onsubmit="return confirm('Segur que vols eliminar l\'usuari <?= htmlspecialchars($user['name'], ENT_QUOTES) ?>? Els seus posts també s\'eliminaran.')">
                                            <?= \App\Infrastructure\Security\Csrf::field() ?>
                                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="admin-you-badge">Tu</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </main>

</section>