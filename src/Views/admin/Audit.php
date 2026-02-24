<?php
// views/admin/audit.php
/** @var array $entries */

$actionLabels = [
    'delete_user'        => 'Usuari eliminat',
    'delete_post'        => 'Post eliminat',
    'switch_post_status' => 'Estat canviat',
    'restore_user'       => 'Usuari restaurat',
    'restore_post'       => 'Post restaurat',
];
$actionBadges = [
    'delete_user'        => 'danger',
    'delete_post'        => 'danger',
    'switch_post_status' => 'primary',
    'restore_user'       => 'success',
    'restore_post'       => 'success',
];
?>

<div class="d-flex" style="min-height: calc(100vh - 56px)">

    <?php require APP_ROOT . '/src/Views/admin/sidebar.php'; ?>

    <main class="flex-grow-1 p-4 bg-light">

        <div class="d-flex align-items-center gap-3 mb-2">
            <h1 class="h4 fw-bold mb-0">Historial d'accions</h1>
            <span class="badge bg-primary"><?= count($entries) ?></span>
        </div>
        <p class="text-muted small mb-4">
            Les entrades s'eliminen automàticament als 9 mesos. Pots restaurar entitats eliminades o esborrar entrades manualment.
        </p>

        <?php if (empty($entries)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-clock-history fs-1 d-block mb-3"></i>
                No hi ha entrades al historial.
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Acció</th>
                                <th>Entitat</th>
                                <th>Snapshot</th>
                                <th>Admin</th>
                                <th>Data</th>
                                <th>Expira</th>
                                <th class="text-end">Accions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entries as $entry): ?>
                                <?php
                                    $data = json_decode($entry['entity_data'], true);
                                    $isRestorable = in_array($entry['action'], ['delete_user', 'delete_post'], true);
                                ?>
                                <tr>
                                    <td class="text-muted small"><?= (int) $entry['id'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $actionBadges[$entry['action']] ?? 'secondary' ?>">
                                            <?= $actionLabels[$entry['action']] ?? htmlspecialchars($entry['action'], ENT_QUOTES) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        <?= htmlspecialchars($entry['entity_type'], ENT_QUOTES) ?>
                                        #<?= (int) $entry['entity_id'] ?>
                                    </td>
                                    <td style="max-width: 200px;">
                                        <?php if ($entry['entity_type'] === 'user'): ?>
                                            <span class="fw-semibold small"><?= htmlspecialchars($data['name'] ?? '—', ENT_QUOTES) ?></span><br>
                                            <span class="text-muted small"><?= htmlspecialchars($data['email'] ?? '—', ENT_QUOTES) ?></span>
                                        <?php elseif ($entry['entity_type'] === 'post'): ?>
                                            <span class="fw-semibold small"><?= htmlspecialchars(mb_substr($data['title'] ?? '—', 0, 50), ENT_QUOTES) ?></span><br>
                                            <span class="text-muted small">Estat: <?= htmlspecialchars($data['status'] ?? '—', ENT_QUOTES) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small"><?= htmlspecialchars(mb_substr($entry['entity_data'], 0, 80), ENT_QUOTES) ?>...</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small"><?= htmlspecialchars($entry['admin_name'], ENT_QUOTES) ?></td>
                                    <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($entry['created_at'])) ?></td>
                                    <td class="text-muted small"><?= date('d/m/Y', strtotime($entry['expires_at'])) ?></td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <?php if ($isRestorable): ?>
                                                <form method="POST"
                                                      action="<?= BASE_PATH ?>/admin/audit/<?= (int) $entry['id'] ?>/restore"
                                                      onsubmit="return confirm('Segur que vols restaurar aquesta entitat?')">
                                                    <?= \App\Infrastructure\Security\Csrf::field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST"
                                                  action="<?= BASE_PATH ?>/admin/audit/<?= (int) $entry['id'] ?>/delete"
                                                  onsubmit="return confirm('Eliminar permanentment aquesta entrada?')">
                                                <?= \App\Infrastructure\Security\Csrf::field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
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