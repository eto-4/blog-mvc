<?php
// views/auth/register.php

use App\Infrastructure\Security\Csrf;

/** @var array $errors */
$errors = $errors ?? [];
?>

<div class="auth-wrapper">
    <div class="auth-card">

        <h1 class="h4 fw-bold text-center mb-4">Crear compte</h1>

        <form method="POST" action="<?= BASE_PATH ?>/register">
            <?= Csrf::field() ?>

            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES) ?>"
                    placeholder="El teu nom"
                    required
                >
                <?php if (!empty($errors['name'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($errors['name'], ENT_QUOTES) ?>
                    </div>
                <?php endif; ?>
            </div>

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

            <div class="mb-3">
                <label for="password" class="form-label">Contrasenya</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                    placeholder="Mínim 8 caràcters, 1 majúscula, 1 número, 1 símbol"
                    required
                >
                <?php if (!empty($errors['password'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($errors['password'], ENT_QUOTES) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Repeteix la contrasenya</label>
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="form-control <?= !empty($errors['password_confirmation']) ? 'is-invalid' : '' ?>"
                    placeholder="Repeteix la contrasenya"
                    required
                >
                <?php if (!empty($errors['password_confirmation'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($errors['password_confirmation'], ENT_QUOTES) ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-person-plus me-2"></i>Registrar-se
            </button>
        </form>

        <hr class="my-3">

        <p class="text-center text-muted small mb-0">
            Ja tens compte?
            <a href="<?= BASE_PATH ?>/login" class="text-decoration-none">Inicia sessió</a>
        </p>

    </div>
</div>