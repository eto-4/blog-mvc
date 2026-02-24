<?php
// views/auth/register.php

use App\Infrastructure\Security\Csrf;

/** @var array $errors */
$errors = $errors ?? [];
?>

<section class="auth-section">
    <div class="auth-card">
        <h1 class="auth-title">Crear compte</h1>

        <form method="POST" action="<?= BASE_PATH ?>/register">
            <?= Csrf::field() ?>

            <div class="form-group">
                <label for="name">Nom</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES) ?>"
                    placeholder="El teu nom..."
                    required
                >
                <?php if (!empty($errors['name'])): ?>
                    <p class="field-error"><?= htmlspecialchars($errors['name'], ENT_QUOTES) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES) ?>"
                    placeholder="correu@exemple.com"
                    required
                >
                <?php if (!empty($errors['email'])): ?>
                    <p class="field-error"><?= htmlspecialchars($errors['email'], ENT_QUOTES) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Contrasenya</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Mínim 8 caràcters, 1 majúscula, 1 número, 1 símbol"
                    required
                >
                <?php if (!empty($errors['password'])): ?>
                    <p class="field-error"><?= htmlspecialchars($errors['password'], ENT_QUOTES) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Repeteix la contrasenya</label>
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    placeholder="Repeteix la contrasenya"
                    required
                >
                <?php if (!empty($errors['password_confirmation'])): ?>
                    <p class="field-error"><?= htmlspecialchars($errors['password_confirmation'], ENT_QUOTES) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary auth-submit">Registrar-se</button>
        </form>

        <p class="auth-footer">
            Ja tens compte? <a href="<?= BASE_PATH ?>/login">Inicia sessió</a>
        </p>
    </div>
</section>