<?php

use App\Infrastructure\Security\Csrf;

/** @var array $errors Opcionalment pot contenir errors de validació */
$errors = $errors ?? [];
?>

<section class="auth-form">
    <h1>Registrar-se</h1>

    <form method="POST" action="<?= BASE_PATH ?>/register">
        <?= Csrf::field(); ?>

        <div class="form-group">
            <label for="name">Nom</label>
            <input
                type="text"
                name="name"
                id="name"
                value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES) ?>"
                required
            >
            <?php if (!empty($errors['name'])): ?>
                <p class="error"><?= htmlspecialchars($errors['name'], ENT_QUOTES) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                name="email"
                id="email"
                value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES) ?>"
                required
            >
            <?php if (!empty($errors['email'])): ?>
                <p class="error"><?= htmlspecialchars($errors['email'], ENT_QUOTES) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Contrasenya</label>
            <input
                type="password"
                name="password"
                id="password"
                required
            >
            <?php if (!empty($errors['password'])): ?>
                <p class="error"><?= htmlspecialchars($errors['password'], ENT_QUOTES) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Repeteix la contrasenya</label>
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                required
            >
            <?php if (!empty($errors['password_confirmation'])): ?>
                <p class="error"><?= htmlspecialchars($errors['password_confirmation'], ENT_QUOTES) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit">Registrar-se</button>
    </form>

    <p>Ja tens compte? <a href="<?= BASE_PATH ?>/login">Inicia sessió</a></p>
</section>

