<?php
// views/layouts/header.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin    = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Blog MVC</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

        <!-- CSS personalitzat -->
        <link href="<?= BASE_PATH ?>/assets/css/style.css" rel="stylesheet">
    </head>
    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
            <div class="container-xl">

                <a class="navbar-brand fw-semibold fs-4" href="<?= BASE_PATH ?>/">
                    Blog MVC
                </a>

                <button class="navbar-toggler" type="button"
                        data-bs-toggle="collapse" data-bs-target="#mainNav"
                        aria-controls="mainNav" aria-expanded="false">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">

                    <!-- Nav esquerra -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_PATH ?>/">
                                <i class="bi bi-house-door me-1"></i>Inici
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_PATH ?>/posts">
                                <i class="bi bi-journal-text me-1"></i>Posts
                            </a>
                        </li>
                    </ul>

                    <!-- Nav dreta -->
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-1">

                        <?php if ($isLoggedIn): ?>

                            <?php if ($isAdmin): ?>
                                <li class="nav-item">
                                    <a class="nav-link text-warning fw-semibold" href="<?= BASE_PATH ?>/admin">
                                        <i class="bi bi-shield-lock me-1"></i>Admin
                                    </a>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_PATH ?>/my-posts">
                                    <i class="bi bi-pencil-square me-1"></i>Els meus posts
                                </a>
                            </li>

                            <!-- Dropdown perfil -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                                   href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle fs-5"></i>
                                    <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Perfil', ENT_QUOTES) ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                                    <li>
                                        <a class="dropdown-item" href="<?= BASE_PATH ?>/profile">
                                            <i class="bi bi-person me-2"></i>Perfil
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= BASE_PATH ?>/my-posts/create">
                                            <i class="bi bi-plus-circle me-2"></i>Nou post
                                        </a>
                                    </li>
                                    <?php if ($isAdmin): ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-warning" href="<?= BASE_PATH ?>/admin">
                                                <i class="bi bi-shield-lock me-2"></i>Panell admin
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="<?= BASE_PATH ?>/logout" class="m-0">
                                            <?= \App\Infrastructure\Security\Csrf::field() ?>
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-box-arrow-right me-2"></i>Sortir
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>

                        <?php else: ?>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_PATH ?>/login">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sessió
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-primary btn-sm ms-1" href="<?= BASE_PATH ?>/register">
                                    Registrar-se
                                </a>
                            </li>

                        <?php endif; ?>

                    </ul>
                </div>
            </div>
        </nav>

        <main class="main-content">
            <div class="container-xl py-3">

            <?php
            foreach (['flash_success' => 'success', 'flash_error' => 'danger'] as $key => $type) {
                if (!empty($_SESSION[$key])) {
                    echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">'
                        . '<i class="bi bi-' . ($type === 'success' ? 'check-circle' : 'exclamation-triangle') . ' me-2"></i>'
                        . htmlspecialchars($_SESSION[$key], ENT_QUOTES)
                        . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
                        . '</div>';
                    unset($_SESSION[$key]);
                }
            }
            ?>