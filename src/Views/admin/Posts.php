<?php
// views/admin/posts.php
/** @var array $posts */

$statusLabels = [
    'published' => 'Publicat',
    'draft'     => 'Esborrany',
    'archived'  => 'Arxivat',
];
$statusBadges = [
    'published' => 'success',
    'draft'     => 'secondary',
    'archived'  => 'warning',
];
?>

<div class="d-flex" style="min-height: calc(100vh - 56px)">

    <?php require APP_ROOT . '/src/Views/admin/sidebar.php'; ?>

    <main class="flex-grow-1 p-4 bg-light">

        <div class="d-flex align-items-center gap-3 mb-4">
            <h1 class="h4 fw-bold mb-0">Tots els posts</h1>
            <span class="badge bg-primary"><?= count($posts) ?></span>
        </div>

        <?php if (empty($posts)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
                No hi ha posts.
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Títol</th>
                                <th>Autor</th>
                                <th>Estat</th>
                                <th>Visualitzacions</th>
                                <th>Publicat</th>
                                <th>Creat</th>
                                <th>Canviar estat</th>
                                <th class="text-end">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td class="text-muted small"><?= (int) $post['id'] ?></td>
                                    <td style="max-width: 260px;">
                                        <?php if ($post['status'] === 'published'): ?>
                                            <a href="<?= BASE_PATH ?>/posts/<?= htmlspecialchars($post['slug'], ENT_QUOTES) ?>"
                                               class="text-decoration-none fw-semibold" target="_blank">
                                                <?= htmlspecialchars($post['title'], ENT_QUOTES) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="fw-semibold text-muted">
                                                <?= htmlspecialchars($post['title'], ENT_QUOTES) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_PATH ?>/author/<?= (int) $post['author_id'] ?>"
                                           class="text-decoration-none text-muted small">
                                            <?= htmlspecialchars($post['author_name'], ENT_QUOTES) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $statusBadges[$post['status']] ?? 'secondary' ?>">
                                            <?= $statusLabels[$post['status']] ?? $post['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <i class="bi bi-eye text-muted me-1"></i><?= number_format((int) $post['views_count']) ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= $post['published_at'] ? date('d/m/Y', strtotime($post['published_at'])) : '—' ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                    </td>
                                    <td>
                                        <form method="POST"
                                              action="<?= BASE_PATH ?>/admin/posts/<?= (int) $post['id'] ?>/status">
                                            <?= \App\Infrastructure\Security\Csrf::field() ?>
                                            <select name="status" class="form-select form-select-sm"
                                                    onchange="this.form.submit()" style="width: auto;">
                                                <?php foreach ($statusLabels as $val => $label): ?>
                                                    <option value="<?= $val ?>"
                                                        <?= $post['status'] === $val ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <form method="POST"
                                              action="<?= BASE_PATH ?>/admin/posts/<?= (int) $post['id'] ?>/delete"
                                              onsubmit="return confirm('Segur que vols eliminar aquest post?')">
                                            <?= \App\Infrastructure\Security\Csrf::field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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