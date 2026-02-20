<?php
// views/posts/my-posts.php

/** @var array $posts */

$statusLabels = [
    'published' => 'Publicat',
    'draft'     => 'Esborrany',
    'archived'  => 'Arxivat',
];
$statusClasses = [
    'published' => 'status-published',
    'draft'     => 'status-draft',
    'archived'  => 'status-archived',
];
?>

<section class="tasks">

    <div class="tasks-header">
        <h1 class="section-title">Els meus posts</h1>
        <a href="<?= BASE_PATH ?>/my-posts/create" class="btn btn-primary">+ Nou post</a>
    </div>

    <?php if (empty($posts)): ?>
        <p class="empty-state">Encara no has creat cap post. <a href="<?= BASE_PATH ?>/my-posts/create">Crea el primer!</a></p>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="tasks-table">
                <thead>
                    <tr>
                        <th>Títol</th>
                        <th>Estat</th>
                        <th>Visualitzacions</th>
                        <th>Creat</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <?php if ($post['status'] === 'published'): ?>
                                    <a href="<?= BASE_PATH ?>/posts/<?= htmlspecialchars($post['slug'], ENT_QUOTES) ?>">
                                        <?= htmlspecialchars($post['title'], ENT_QUOTES) ?>
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($post['title'], ENT_QUOTES) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge <?= $statusClasses[$post['status']] ?? '' ?>">
                                    <?= $statusLabels[$post['status']] ?? $post['status'] ?>
                                </span>
                            </td>
                            <td>👁 <?= (int) $post['views_count'] ?></td>
                            <td><?= date('d/m/Y', strtotime($post['created_at'])) ?></td>
                            <td class="actions">
                                <!-- Editar -->
                                <a href="<?= BASE_PATH ?>/my-posts/<?= (int) $post['id'] ?>/edit"
                                   class="btn btn-sm btn-primary">Editar</a>

                                <!-- Publicar / Despublicar -->
                                <form method="POST"
                                      action="<?= BASE_PATH ?>/my-posts/<?= (int) $post['id'] ?>/publish"
                                      style="display:inline">
                                    <?= \App\Infrastructure\Security\Csrf::field() ?>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <?= $post['status'] === 'published' ? 'Despublicar' : 'Publicar' ?>
                                    </button>
                                </form>

                                <!-- Eliminar -->
                                <form method="POST"
                                      action="<?= BASE_PATH ?>/my-posts/<?= (int) $post['id'] ?>/delete"
                                      style="display:inline"
                                      onsubmit="return confirm('Segur que vols eliminar aquest post?')">
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

</section>