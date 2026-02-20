<?php
// views/posts/show.php

/** @var App\Domain\Models\Post $post */
?>

<article class="post-single">

    <header class="post-single-header">
        <div class="post-breadcrumb">
            <a href="<?= BASE_PATH ?>/posts">← Tots els posts</a>
        </div>

        <h1 class="post-single-title">
            <?= htmlspecialchars($post->title, ENT_QUOTES) ?>
        </h1>

        <div class="post-single-meta">
            <span>
                Per
                <a href="<?= BASE_PATH ?>/author/<?= (int) $post->author_id ?>">
                    <?= htmlspecialchars($post->author_name ?? 'Autor desconegut', ENT_QUOTES) ?>
                </a>
            </span>
            <span>
                <?= $post->published_at ? date('d/m/Y', strtotime($post->published_at)) : '' ?>
            </span>
            <span>👁 <?= (int) $post->views_count ?> visualitzacions</span>
        </div>
    </header>

    <div class="post-single-content">
        <?= nl2br(htmlspecialchars($post->content, ENT_QUOTES)) ?>
    </div>

    <?php if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $post->author_id): ?>
        <div class="post-single-actions">
            <a href="<?= BASE_PATH ?>/my-posts/<?= (int) $post->id ?>/edit" class="btn btn-primary btn-sm">
                Editar
            </a>
            <form method="POST" action="<?= BASE_PATH ?>/my-posts/<?= (int) $post->id ?>/delete"
                  onsubmit="return confirm('Segur que vols eliminar aquest post?')">
                <?= \App\Infrastructure\Security\Csrf::field() ?>
                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
            </form>
        </div>
    <?php endif; ?>

</article>