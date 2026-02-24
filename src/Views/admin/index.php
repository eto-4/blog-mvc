<?php
// views/admin/index.php
/** @var array $stats */
?>

<div class="d-flex">

    <?php require APP_ROOT . '/src/Views/admin/sidebar.php'; ?>

    <main class="flex-grow-1 p-4 bg-light">

        <h1 class="h4 fw-bold mb-4">Dashboard</h1>

        <!-- Estadístiques -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-5">
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="fs-2 fw-bold text-primary"><?= (int) $stats['total_users'] ?></div>
                        <div class="fw-semibold">Usuaris totals</div>
                        <div class="text-muted small">+<?= (int) $stats['new_users_30d'] ?> aquest mes</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="fs-2 fw-bold text-primary"><?= (int) $stats['total_posts'] ?></div>
                        <div class="fw-semibold">Posts totals</div>
                        <div class="text-muted small">+<?= (int) $stats['new_posts_30d'] ?> aquest mes</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="fs-2 fw-bold text-success"><?= (int) $stats['published_posts'] ?></div>
                        <div class="fw-semibold">Posts publicats</div>
                        <div class="text-muted small">
                            <?= (int) $stats['draft_posts'] ?> esborranys · <?= (int) $stats['archived_posts'] ?> arxivats
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="fs-2 fw-bold text-info"><?= number_format((int) $stats['total_views']) ?></div>
                        <div class="fw-semibold">Visualitzacions totals</div>
                        <div class="text-muted small">Suma de tots els posts</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accions ràpides -->
        <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Accions ràpides</h2>
        <div class="d-flex gap-3 flex-wrap">
            <a href="<?= BASE_PATH ?>/admin/users" class="card border-0 shadow-sm text-decoration-none text-dark" style="min-width:160px">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-2 text-primary d-block mb-2"></i>
                    Gestionar usuaris
                </div>
            </a>
            <a href="<?= BASE_PATH ?>/admin/posts" class="card border-0 shadow-sm text-decoration-none text-dark" style="min-width:160px">
                <div class="card-body text-center">
                    <i class="bi bi-journal-text fs-2 text-primary d-block mb-2"></i>
                    Gestionar posts
                </div>
            </a>
            <a href="<?= BASE_PATH ?>/admin/audit" class="card border-0 shadow-sm text-decoration-none text-dark" style="min-width:160px">
                <div class="card-body text-center">
                    <i class="bi bi-clock-history fs-2 text-primary d-block mb-2"></i>
                    Veure historial
                </div>
            </a>
        </div>

    </main>
</div>