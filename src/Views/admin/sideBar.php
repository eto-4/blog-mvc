<?php
// views/admin/sidebar.php

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function isActive(string $path): string {
    global $currentPath;
    return str_ends_with($currentPath, BASE_PATH . $path) ? 'active' : '';
}
?>

<div class="admin-sidebar d-flex flex-column p-3" style="min-width: 220px;">

    <p class="admin-sidebar nav-section-title text-uppercase fw-bold mb-2 px-2">
        Administració
    </p>

    <nav class="nav flex-column gap-1">
        <a href="<?= BASE_PATH ?>/admin"
           class="nav-link <?= isActive('/admin') ?>">
            <i class="bi bi-bar-chart me-2"></i>Dashboard
        </a>
        <a href="<?= BASE_PATH ?>/admin/users"
           class="nav-link <?= isActive('/admin/users') ?>">
            <i class="bi bi-people me-2"></i>Usuaris
        </a>
        <a href="<?= BASE_PATH ?>/admin/posts"
           class="nav-link <?= isActive('/admin/posts') ?>">
            <i class="bi bi-journal-text me-2"></i>Posts
        </a>
        <a href="<?= BASE_PATH ?>/admin/audit"
           class="nav-link <?= isActive('/admin/audit') ?>">
            <i class="bi bi-clock-history me-2"></i>Historial
        </a>
    </nav>

    <hr class="border-secondary my-3">

    <nav class="nav flex-column">
        <a href="<?= BASE_PATH ?>/" class="nav-link">
            <i class="bi bi-arrow-left me-2"></i>Tornar al blog
        </a>
    </nav>

</div>