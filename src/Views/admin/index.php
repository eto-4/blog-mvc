<?php
// views/admin/index.php

/** @var array $stats */
?>

<section class="admin-layout">

    <?php require APP_ROOT . '/src/Views/admin/sidebar.php'; ?>

    <!-- Contingut principal -->
    <main class="admin-main">

        <h1 class="admin-page-title">Dashboard</h1>

        <!-- Estadístiques globals -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <span class="admin-stat-number"><?= $stats['total_users'] ?></span>
                <span class="admin-stat-label">Usuaris totals</span>
                <span class="admin-stat-sub">+<?= $stats['new_users_30d'] ?> aquest mes</span>
            </div>
            <div class="admin-stat-card">
                <span class="admin-stat-number"><?= $stats['total_posts'] ?></span>
                <span class="admin-stat-label">Posts totals</span>
                <span class="admin-stat-sub">+<?= $stats['new_posts_30d'] ?> aquest mes</span>
            </div>
            <div class="admin-stat-card">
                <span class="admin-stat-number"><?= $stats['published_posts'] ?></span>
                <span class="admin-stat-label">Posts publicats</span>
                <span class="admin-stat-sub"><?= $stats['draft_posts'] ?> esborranys · <?= $stats['archived_posts'] ?> arxivats</span>
            </div>
            <div class="admin-stat-card">
                <span class="admin-stat-number"><?= number_format($stats['total_views']) ?></span>
                <span class="admin-stat-label">Visualitzacions totals</span>
                <span class="admin-stat-sub">Suma de tots els posts</span>
            </div>
        </div>

        <!-- Accesos ràpids -->
        <div class="admin-quick-actions">
            <h2>Accions ràpides</h2>
            <div class="admin-quick-grid">
                <a href="<?= BASE_PATH ?>/admin/users" class="admin-quick-card">
                    <span class="admin-quick-icon">&#x1F464;</span>
                    <span>Gestionar usuaris</span>
                </a>
                <a href="<?= BASE_PATH ?>/admin/posts" class="admin-quick-card">
                    <span class="admin-quick-icon">&#x1F5CE;</span>
                    <span>Gestionar posts</span>
                </a>
                <a href="<?= BASE_PATH ?>/admin/audit" class="admin-quick-card">
                    <span class="admin-quick-icon">&#x1F554;</span>
                    <span>Veure historial</span>
                </a>
            </div>
        </div>

    </main>

</section>