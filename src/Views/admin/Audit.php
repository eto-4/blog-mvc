<?php
// views/admin/audit.php

/** @var array $entries */

$actionLabels = [
    'delete_user'         => 'Usuari eliminat',
    'delete_post'         => 'Post eliminat',
    'switch_post_status'  => 'Estat canviat',
    'restore_user'        => 'Usuari restaurat',
    'restore_post'        => 'Post restaurat',
];
$actionClasses = [
    'delete_user'         => 'audit-delete',
    'delete_post'         => 'audit-delete',
    'switch_post_status'  => 'audit-switch',
    'restore_user'        => 'audit-restore',
    'restore_post'        => 'audit-restore',
];
?>

<section class="admin-layout">

    <?php require APP_ROOT . '/src/Views/admin/sidebar.php'; ?>

    <main class="admin-main">

        <div class="admin-page-header">
            <h1 class="admin-page-title">Historial d'accions</h1>
            <span class="admin-count"><?= count($entries) ?> entrades actives</span>
        </div>

        <p class="admin-info-text">
            Les entrades s'eliminen automàticament als 9 mesos. Pots restaurar entitats eliminades o esborrar entrades manualment.
        </p>

        <?php if (empty($entries)): ?>
            <p class="empty-state">No hi ha entrades al historial.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Acció</th>
                            <th>Entitat</th>
                            <th>Dades snapshot</th>
                            <th>Admin</th>
                            <th>Data</th>
                            <th>Expira</th>
                            <th>Accions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $entry): ?>
                            <?php
                                $data = json_decode($entry['entity_data'], true);
                                $isRestorable = in_array($entry['action'], ['delete_user', 'delete_post'], true);
                            ?>
                            <tr>
                                <td><?= (int) $entry['id'] ?></td>
                                <td>
                                    <span class="audit-badge <?= $actionClasses[$entry['action']] ?? '' ?>">
                                        <?= $actionLabels[$entry['action']] ?? htmlspecialchars($entry['action'], ENT_QUOTES) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="audit-entity">
                                        <?= htmlspecialchars($entry['entity_type'], ENT_QUOTES) ?>
                                        #<?= (int) $entry['entity_id'] ?>
                                    </span>
                                </td>
                                <td class="audit-snapshot">
                                    <?php if ($entry['entity_type'] === 'user'): ?>
                                        <strong><?= htmlspecialchars($data['name'] ?? '—', ENT_QUOTES) ?></strong><br>
                                        <small><?= htmlspecialchars($data['email'] ?? '—', ENT_QUOTES) ?></small>
                                    <?php elseif ($entry['entity_type'] === 'post'): ?>
                                        <strong><?= htmlspecialchars(mb_substr($data['title'] ?? '—', 0, 50), ENT_QUOTES) ?></strong><br>
                                        <small>Estat: <?= htmlspecialchars($data['status'] ?? '—', ENT_QUOTES) ?></small>
                                    <?php else: ?>
                                        <small><?= htmlspecialchars(mb_substr($entry['entity_data'], 0, 80), ENT_QUOTES) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($entry['admin_name'], ENT_QUOTES) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($entry['created_at'])) ?></td>
                                <td>
                                    <span class="audit-expires">
                                        <?= date('d/m/Y', strtotime($entry['expires_at'])) ?>
                                    </span>
                                </td>
                                <td class="admin-actions">
                                    <?php if ($isRestorable): ?>
                                        <form method="POST"
                                              action="<?= BASE_PATH ?>/admin/audit/<?= (int) $entry['id'] ?>/restore"
                                              style="display:inline"
                                              onsubmit="return confirm('Segur que vols restaurar aquesta entitat?')">
                                            <?= \App\Infrastructure\Security\Csrf::field() ?>
                                            <button type="submit" class="btn btn-sm btn-success">Restaurar</button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="POST"
                                          action="<?= BASE_PATH ?>/admin/audit/<?= (int) $entry['id'] ?>/delete"
                                          style="display:inline"
                                          onsubmit="return confirm('Eliminar permanentment aquesta entrada del historial?')">
                                        <?= \App\Infrastructure\Security\Csrf::field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </main>

</section>