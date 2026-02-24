<?php
// views/home/home.php
$isLoggedIn = isset($_SESSION['user_id']);
?>

<div class="home-hero">
    <h1 class="home-title">Benvingut al Blog MVC</h1>

    <p class="home-text">
        Descobreix articles escrits per la comunitat. Comparteix les teves idees, explora contingut nou i gestiona els teus posts des del teu espai personal.
    </p>

    <div class="home-actions">
        <a href="<?= BASE_PATH ?>/posts" class="btn btn-primary btn-lg">
            Veure tots els posts
        </a>

        <?php if ($isLoggedIn): ?>
            <a href="<?= BASE_PATH ?>/my-posts/create" class="btn btn-outline-secondary btn-lg">
                Escriure un post
            </a>
        <?php else: ?>
            <a href="<?= BASE_PATH ?>/register" class="btn btn-outline-secondary btn-lg">
                Crear un compte
            </a>
        <?php endif; ?>
    </div>
</div>