<?php
// views/posts/show.php

/** @var App\Domain\Models\Post $post */
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">

        <article>
            <!-- Breadcrumb -->
            <nav class="mb-3">
                <a href="<?= BASE_PATH ?>/posts" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i>Tots els posts
                </a>
            </nav>

            <!-- Capçalera -->
            <header class="mb-4">
                <h1 class="fw-bold mb-3">
                    <?= htmlspecialchars($post->title, ENT_QUOTES) ?>
                </h1>
                <div class="d-flex flex-wrap gap-3 text-muted small">
                    <span>
                        <i class="bi bi-person me-1"></i>
                        <a href="<?= BASE_PATH ?>/author/<?= (int) $post->author_id ?>"
                           class="text-decoration-none text-muted">
                            <?= htmlspecialchars($post->author_name ?? 'Autor desconegut', ENT_QUOTES) ?>
                        </a>
                    </span>
                    <?php if ($post->published_at): ?>
                        <span>
                            <i class="bi bi-calendar3 me-1"></i>
                            <?= date('d/m/Y', strtotime($post->published_at)) ?>
                        </span>
                    <?php endif; ?>
                    <span>
                        <i class="bi bi-eye me-1"></i><?= (int) $post->views_count ?> visualitzacions
                    </span>
                </div>
            </header>

            <hr class="mb-4">

            <!-- Contingut -->
            <div class="post-content lh-lg">
                <?= nl2br(htmlspecialchars($post->content, ENT_QUOTES)) ?>
            </div>

            <!-- Accions autor -->
            <?php if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $post->author_id): ?>
                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <a href="<?= BASE_PATH ?>/my-posts/<?= (int) $post->id ?>/edit"
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    <form method="POST"
                          action="<?= BASE_PATH ?>/my-posts/<?= (int) $post->id ?>/delete"
                          onsubmit="return confirm('Segur que vols eliminar aquest post?')">
                        <?= \App\Infrastructure\Security\Csrf::field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash me-1"></i>Eliminar
                        </button>
                    </form>
                </div>
            <?php endif; ?>

        </article>

    </div>
</div>