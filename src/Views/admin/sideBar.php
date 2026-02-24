<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath    = BASE_PATH; // per comparar correctament

function isActive(string $path): string {
    global $currentPath, $basePath;
    return str_ends_with($currentPath, $basePath . $path) ? 'active' : '';
}
?>
<aside class="admin-sidebar">
    <nav class="admin-nav">
        <p class="admin-nav-title">Administració</p>
        <ul>
            <li><a href="<?= BASE_PATH ?>/admin"       class="<?= isActive('/admin') ?>">&#x1F4CA; Dashboard</a></li>
            <li><a href="<?= BASE_PATH ?>/admin/users"  class="<?= isActive('/admin/users') ?>">&#x1F464; Usuaris</a></li>
            <li><a href="<?= BASE_PATH ?>/admin/posts"  class="<?= isActive('/admin/posts') ?>">&#x1F5CE; Posts</a></li>
            <li><a href="<?= BASE_PATH ?>/admin/audit"  class="<?= isActive('/admin/audit') ?>">&#x1F554; Historial</a></li>
        </ul>
        <hr>
        <ul>
            <li><a href="<?= BASE_PATH ?>/">← Tornar al blog</a></li>
        </ul>
    </nav>
</aside>