<?php
// views/home/404.php
http_response_code(404);
?>

<div class="error-page">
    <h1 class="error-code">404</h1>
    <h2 class="error-title">Pàgina no trobada</h2>
    <p class="error-text">
        Ho sentim, la pàgina que estàs buscant no existeix.
    </p>
    <a href="<?= BASE_PATH ?>/" class="btn-primary">
        Tornar a l'inici
    </a>
</div>
