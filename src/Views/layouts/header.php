<?php
// views/layouts/header.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once APP_ROOT . '/helpers/FlashMessages.php';
?>
<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Aplicació MVC</title>

        <!-- CSS general -->
        <link href="<?= BASE_PATH ?>/assets/css/style.css" rel="stylesheet">
    </head>
    <body>
        
        <header class="main-header">
            <nav class="nav">
                <div class="nav-left">
                    <a class="brand" href="<?= BASE_PATH ?>/">MVC App</a>
                </div>
        
                <ul class="nav-right">
                    <li><a href="<?= BASE_PATH ?>/">Inici</a></li>
                    <li><a href="<?= BASE_PATH ?>/tasques">Tasques</a></li>
                    <li><a href="<?= BASE_PATH ?>/tasques/create">Nova Tasca</a></li>
                </ul>
            </nav>
        </header>
        
        <main class="main-content">
            <?php FlashMessages::display(); ?>
