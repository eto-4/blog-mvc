<?php
// views/posts/index.php

/** @var array  $posts       */
/** @var int    $totalPages  */
/** @var int    $currentPage */
/** @var string $searchQuery Opcional, present quan venim de /search */

$isSearch = isset($searchQuery);
?>

<section class="posts-section">

    <?php if ($isSearch): ?>
        <div class="posts-header">
            <h1 class="section-title">
                Resultats per: <em><?= htmlspecialchars($searchQuery, ENT_QUOTES) ?></em>
            </h1>
            <a href="<?= BASE_PATH ?>/posts" class="btn btn-sm btn-primary">← Tots els posts</a>
        </div>
    <?php else: ?>
        <div class="posts-header">
            <h1 class="section-title">Posts</h1>
            <form method="GET" action="<?= BASE_PATH ?>/search" class="search-form">
                <input type="text" name="q" placeholder="Cerca posts..." value="">
                <button type="submit" class="btn btn-sm btn-primary">Cerca</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
        <p class="empty-state">
            <?= $isSearch ? 'Cap resultat trobat.' : 'Encara no hi ha posts publicats.' ?>
        </p>
    <?php else: ?>
        <div class="posts-grid">
            <?php foreach ($posts as $post): ?>
                <article class="post-card">
                    <div class="post-card-body">
                        <h2 class="post-card-title">
                            <a href="<?= BASE_PATH ?>/posts/<?= htmlspecialchars($post['slug'], ENT_QUOTES) ?>">
                                <?= htmlspecialchars($post['title'], ENT_QUOTES) ?>
                            </a>
                        </h2>

                        <p class="post-card-excerpt">
                            <?= htmlspecialchars($post['excerpt'] ?? '', ENT_QUOTES) ?>
                        </p>
                    </div>

                    <div class="post-card-footer">
                        <span class="post-meta">
                            Per
                            <a href="<?= BASE_PATH ?>/author/<?= (int) $post['author_id'] ?>">
                                <?= htmlspecialchars($post['author_name'] ?? 'Autor desconegut', ENT_QUOTES) ?>
                            </a>
                        </span>
                        <span class="post-meta">
                            <?= $post['published_at'] ? date('d/m/Y', strtotime($post['published_at'])) : '' ?>
                        </span>
                        <span class="post-meta">👁 <?= (int) $post['views_count'] ?></span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>" class="btn btn-sm btn-primary">← Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>"
                       class="btn btn-sm <?= $i === $currentPage ? 'btn-primary' : 'btn-outline-secondary' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>" class="btn btn-sm btn-primary">Següent →</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

</section>