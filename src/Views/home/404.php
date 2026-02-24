<?php
// views/home/404.php
http_response_code(404);
?>



<div class="d-flex flex-column align-items-center justify-content-center text-center py-5 my-4">

    <h1 class="display-1 fw-bold text-muted">404</h1>

    <h2 class="h3 fw-semibold mb-3">Pàgina no trobada</h2>

    <p class="text-muted mb-4" style="max-width: 480px;">
        Ho sentim, la pàgina que estàs buscant no existeix o ha estat eliminada.
    </p>

    <a href="<?= BASE_PATH ?>/" class="btn btn-primary px-4">
        <i class="bi bi-house-door me-2"></i>Tornar a l'inici
    </a>

</div>