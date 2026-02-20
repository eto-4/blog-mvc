<?php
// views/layouts/header.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Blog MVC</title>
        <link href="<?= BASE_PATH ?>/assets/css/style.css" rel="stylesheet">
    </head>
    <body>

        <header class="main-header">
            <nav class="nav">
                <div class="nav-left">
                    <a class="brand" href="<?= BASE_PATH ?>/">Blog MVC</a>
                </div>

                <ul class="nav-right">
                    <li><a href="<?= BASE_PATH ?>/">Inici</a></li>
                    <li><a href="<?= BASE_PATH ?>/posts">Posts</a></li>
                    <li><a href="<?= BASE_PATH ?>/search">Cerca</a></li>

                    <?php if ($isLoggedIn): ?>
                        <li><a href="<?= BASE_PATH ?>/my-posts">Els meus posts</a></li>
                        <li><a href="<?= BASE_PATH ?>/my-posts/create">Nou post</a></li>
                        <li><a href="<?= BASE_PATH ?>/profile">Perfil</a></li>
                        <li>
                            <form method="POST" action="<?= BASE_PATH ?>/logout" style="margin:0">
                                <?= \App\Infrastructure\Security\Csrf::field() ?>
                                <button type="submit" class="btn-nav-logout">Sortir</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a href="<?= BASE_PATH ?>/login">Entrar</a></li>
                        <li><a href="<?= BASE_PATH ?>/register">Registrar-se</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <main class="main-content">
            <?php
            foreach (['flash_success' => 'flash-success', 'flash_error' => 'flash-error'] as $key => $class) {
                if (!empty($_SESSION[$key])) {
                    echo '<div class="' . $class . '">' . htmlspecialchars($_SESSION[$key], ENT_QUOTES) . '</div>';
                    unset($_SESSION[$key]);
                }
            }
            ?>