<?php
// views/auth/login.php

use App\Infrastructure\Security\Csrf;

/** @var array $errors */
$errors = $errors ?? [];
?>

<div class="auth-wrapper">
    <div class="auth-card">

        <h1 class="h4 fw-bold text-center mb-4">Iniciar sessió</h1>

        <form method="POST" action="<?= BASE_PATH ?>/login">
            <?= Csrf::field() ?>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES) ?>"
                    placeholder="correu@exemple.com"
                    required
                >
                <?php if (!empty($errors['email'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($errors['email'], ENT_QUOTES) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Contrasenya</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                    placeholder="La teva contrasenya"
                    required
                >
                <?php if (!empty($errors['password'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($errors['password'], ENT_QUOTES) ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
            </button>
        </form>

        <hr class="my-3">

        <p class="text-center text-muted small mb-0">
            No tens compte?
            <a href="<?= BASE_PATH ?>/register" class="text-decoration-none">Registra't</a>
        </p>

    </div>
</div>