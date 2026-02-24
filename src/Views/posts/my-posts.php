<?php
// views/posts/my-posts.php

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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 fw-bold mb-0">Els meus posts</h1>
    <a href="<?= BASE_PATH ?>/my-posts/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nou post
    </a>
</div>

<?php if (empty($posts)): ?>
    <div class="text-center text-muted py-5">
        <i class="bi bi-journal-plus fs-1 d-block mb-3"></i>
        Encara no has creat cap post.
        <a href="<?= BASE_PATH ?>/my-posts/create" class="d-block mt-2">Crea el primer!</a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Títol</th>
                    <th>Estat</th>
                    <th>Visualitzacions</th>
                    <th>Creat</th>
                    <th class="text-end">Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td>
                            <?php if ($post['status'] === 'published'): ?>
                                <a href="<?= BASE_PATH ?>/posts/<?= htmlspecialchars($post['slug'], ENT_QUOTES) ?>"
                                   class="text-decoration-none fw-semibold">
                                    <?= htmlspecialchars($post['title'], ENT_QUOTES) ?>
                                </a>
                            <?php else: ?>
                                <span class="fw-semibold text-muted">
                                    <?= htmlspecialchars($post['title'], ENT_QUOTES) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $statusBadges[$post['status']] ?? 'secondary' ?>">
                                <?= $statusLabels[$post['status']] ?? $post['status'] ?>
                            </span>
                        </td>
                        <td>
                            <i class="bi bi-eye text-muted me-1"></i><?= (int) $post['views_count'] ?>
                        </td>
                        <td class="text-muted small">
                            <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="<?= BASE_PATH ?>/my-posts/<?= (int) $post['id'] ?>/edit"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form method="POST"
                                      action="<?= BASE_PATH ?>/my-posts/<?= (int) $post['id'] ?>/publish">
                                    <?= \App\Infrastructure\Security\Csrf::field() ?>
                                    <button type="submit"
                                            class="btn btn-sm <?= $post['status'] === 'published' ? 'btn-outline-warning' : 'btn-outline-success' ?>"
                                            title="<?= $post['status'] === 'published' ? 'Arxivar' : 'Publicar' ?>">
                                        <i class="bi bi-<?= $post['status'] === 'published' ? 'archive' : 'send' ?>"></i>
                                    </button>
                                </form>

                                <form method="POST"
                                      action="<?= BASE_PATH ?>/my-posts/<?= (int) $post['id'] ?>/delete"
                                      onsubmit="return confirm('Segur que vols eliminar aquest post?')">
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
<?php endif; ?>