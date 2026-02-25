<?php
// views/posts/index.php

/** @var array  $posts       */
/** @var int    $totalPages  */
/** @var int    $currentPage */
/** @var string $searchQuery */

$isSearch = isset($searchQuery);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <?php if ($isSearch): ?>
        <h1 class="h4 fw-bold mb-0">
            Resultats per: <em class="text-muted"><?= htmlspecialchars($searchQuery, ENT_QUOTES) ?></em>
        </h1>
        <a href="<?= BASE_PATH ?>/posts" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Tots els posts
        </a>
    <?php else: ?>
        <h1 class="h4 fw-bold mb-0">Posts</h1>
        <form method="GET" action="<?= BASE_PATH ?>/search" class="d-flex gap-2">
            <input type="text" name="q" class="form-control form-control-sm"
                   placeholder="Cerca posts..." style="width: 220px;">
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-search"></i>
            </button>
        </form>
    <?php endif; ?>
</div>

<?php if (empty($posts)): ?>
    <div class="text-center text-muted py-5">
        <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
        <?= $isSearch ? 'Cap resultat trobat.' : 'Encara no hi ha posts publicats.' ?>
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-4">
        <?php foreach ($posts as $post): ?>
            <div class="col">
                <article class="post-card card h-100">
                    <div class="card-body d-flex flex-column gap-2">
                        <h2 class="card-title h6 mb-0">
                            <a href="<?= BASE_PATH ?>/posts/<?= htmlspecialchars($post['slug'], ENT_QUOTES) ?>">
                                <?= htmlspecialchars($post['title'], ENT_QUOTES) ?>
                            </a>
                        </h2>
                        <p class="card-text text-muted small flex-grow-1">
                            <?= htmlspecialchars($post['excerpt'] ?? '', ENT_QUOTES) ?>
                        </p>
                        <div class="d-flex gap-3 text-muted small mt-auto pt-2 border-top">
                            <span>
                                <i class="bi bi-person me-1"></i>
                                <a href="<?= BASE_PATH ?>/author/<?= (int) $post['author_id'] ?>"
                                   class="text-decoration-none text-muted">
                                    <?= htmlspecialchars($post['author_name'] ?? 'Autor desconegut', ENT_QUOTES) ?>
                                </a>
                            </span>
                            <?php if ($post['published_at']): ?>
                                <span>
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?= date('d/m/Y', strtotime($post['published_at'])) ?>
                                </span>
                            <?php endif; ?>
                            <span>
                                <i class="bi bi-eye me-1"></i><?= (int) $post['views_count'] ?>
                            </span>
                        </div>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
    
                <!-- Flecha anterior -->
                <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                            &laquo;
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">&laquo;</span>
                    </li>
                <?php endif; ?>
                
                <!-- Números de página -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <!-- Flecha siguiente -->
                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                            &raquo;
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">&raquo;</span>
                    </li>
                <?php endif; ?>
                
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>