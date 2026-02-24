<?php
// views/admin/posts.php

/** @var array $posts */

$statusLabels = [
    'published' => 'Publicat',
    'draft'     => 'Esborrany',
    'archived'  => 'Arxivat',
];
?>

<section class="admin-layout">

    <?php require APP_ROOT . '/src/Views/admin/sidebar.php'; ?>

    <main class="admin-main">

        <div class="admin-page-header">
            <h1 class="admin-page-title">Tots els posts</h1>
            <span class="admin-count"><?= count($posts) ?> posts</span>
        </div>

        <?php if (empty($posts)): ?>
            <p class="empty-state">No hi ha posts.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Títol</th>
                            <th>Autor</th>
                            <th>Estat</th>
                            <th>Visualitzacions</th>
                            <th>Publicat</th>
                            <th>Creat</th>
                            <th>Accions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td><?= (int) $post['id'] ?></td>
                                <td class="admin-post-title">
                                    <?php if ($post['status'] === 'published'): ?>
                                        <a href="<?= BASE_PATH ?>/posts/<?= htmlspecialchars($post['slug'], ENT_QUOTES) ?>" target="_blank">
                                            <?= htmlspecialchars($post['title'], ENT_QUOTES) ?>
                                        </a>
                                    <?php else: ?>
                                        <?= htmlspecialchars($post['title'], ENT_QUOTES) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= BASE_PATH ?>/author/<?= (int) $post['author_id'] ?>">
                                        <?= htmlspecialchars($post['author_name'], ENT_QUOTES) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $post['status'] ?>">
                                        <?= $statusLabels[$post['status']] ?? $post['status'] ?>
                                    </span>
                                </td>
                                <td>👁 <?= number_format((int) $post['views_count']) ?></td>
                                <td><?= $post['published_at'] ? date('d/m/Y', strtotime($post['published_at'])) : '—' ?></td>
                                <td><?= date('d/m/Y', strtotime($post['created_at'])) ?></td>
                                <td class="admin-actions">
                                    <!-- Canviar estat -->
                                    <form method="POST"
                                          action="<?= BASE_PATH ?>/admin/posts/<?= (int) $post['id'] ?>/status"
                                          style="display:inline">
                                        <?= \App\Infrastructure\Security\Csrf::field() ?>
                                        <select name="status" onchange="this.form.submit()" class="admin-status-select">
                                            <?php foreach ($statusLabels as $val => $label): ?>
                                                <option value="<?= $val ?>" <?= $post['status'] === $val ? 'selected' : '' ?>>
                                                    <?= $label ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
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