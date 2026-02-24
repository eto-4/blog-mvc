<?php
// views/user/profile.php

/** @var array $user  */
/** @var array $stats */
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8 d-flex flex-column gap-4">

        <!-- Capçalera perfil -->
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex gap-4 align-items-start p-4">

                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= BASE_PATH ?>/storage/uploads/<?= htmlspecialchars($user['avatar'], ENT_QUOTES) ?>"
                             alt="Avatar"
                             class="avatar-lg">
                    <?php else: ?>
                        <div class="avatar-placeholder-lg">
                            <?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <div class="flex-grow-1">
                    <h1 class="h4 fw-bold mb-1">
                        <?= htmlspecialchars($user['name'], ENT_QUOTES) ?>
                    </h1>
                    <p class="text-primary small mb-1">
                        <?= htmlspecialchars($user['email'], ENT_QUOTES) ?>
                    </p>
                    <?php if (!empty($user['bio'])): ?>
                        <p class="text-muted mb-1">
                            <?= htmlspecialchars($user['bio'], ENT_QUOTES) ?>
                        </p>
                    <?php endif; ?>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-calendar3 me-1"></i>
                        Membre des de <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                    </p>
                </div>

                <!-- Botó editar -->
                <div class="flex-shrink-0">
                    <a href="<?= BASE_PATH ?>/profile/edit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadístiques -->
        <div class="row row-cols-3 g-3">
            <div class="col">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body py-3">
                        <div class="fs-2 fw-bold text-primary"><?= (int) $stats['total_posts'] ?></div>
                        <div class="text-muted small">Posts totals</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body py-3">
                        <div class="fs-2 fw-bold text-success"><?= (int) $stats['published_posts'] ?></div>
                        <div class="text-muted small">Publicats</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body py-3">
                        <div class="fs-2 fw-bold text-info"><?= number_format((int) $stats['total_views']) ?></div>
                        <div class="text-muted small">Visualitzacions</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enllaços -->
        <div class="d-flex gap-2">
            <a href="<?= BASE_PATH ?>/my-posts" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i>Els meus posts
            </a>
            <a href="<?= BASE_PATH ?>/author/<?= (int) $user['id'] ?>" class="btn btn-outline-secondary">
                <i class="bi bi-person me-1"></i>Perfil públic
            </a>
        </div>

    </div>
</div>