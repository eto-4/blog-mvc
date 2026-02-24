<?php
// views/home/home.php
$isLoggedIn = isset($_SESSION['user_id']);
?>

<div class="d-flex flex-column align-items-center justify-content-center text-center py-5 my-4">

    <h1 class="display-5 fw-bold mb-3">Benvingut al Blog MVC</h1>

    <p class="text-muted fs-5 mb-4" style="max-width: 600px;">
        Descobreix articles escrits per la comunitat. Comparteix les teves idees,
        explora contingut nou i gestiona els teus posts des del teu espai personal.
    </p>

    <div class="d-flex gap-3 flex-wrap justify-content-center">
        <a href="<?= BASE_PATH ?>/posts" class="btn btn-primary btn-lg px-4">
            <i class="bi bi-journal-text me-2"></i>Veure tots els posts
        </a>

        <?php if ($isLoggedIn): ?>
            <a href="<?= BASE_PATH ?>/my-posts/create" class="btn btn-outline-secondary btn-lg px-4">
                <i class="bi bi-pencil-square me-2"></i>Escriure un post
            </a>
        <?php else: ?>
            <a href="<?= BASE_PATH ?>/register" class="btn btn-outline-secondary btn-lg px-4">
                <i class="bi bi-person-plus me-2"></i>Crear un compte
            </a>
        <?php endif; ?>
    </div>

</div>