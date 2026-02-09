<?php

use App\Infrastructure\Security\Csrf;

/** @var array $errors Opcionalment pot contenir errors de validació */
$errors = $errors ?? [];
?>

<section class="auth-form">
    <h1>Iniciar sessió</h1>

    <form method="POST" action="<?= BASE_PATH ?>/login">
        <?= Csrf::field(); ?>

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

        <button type="submit">Entrar</button>
    </form>

    <p>No tens compte? <a href="<?= BASE_PATH ?>/register">Registra't</a></p>
</section>

