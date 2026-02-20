<?php
// views/user/edit.php

/** @var array  $user      */
/** @var array  $errors    */
/** @var array  $old       */
/** @var string $csrfToken */

$errors = $errors ?? [];
$old    = $old    ?? [];
?>

<section class="form-section">

    <?php if (!empty($errors)): ?>
        <div class="form-error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error, ENT_QUOTES) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Formulari principal de perfil -->
    <form method="POST" action="<?= BASE_PATH ?>/profile/update" class="form-grid-post">
        <?= \App\Infrastructure\Security\Csrf::field() ?>

        <div class="mTitle">Editar perfil</div>

        <div class="field-title">
            <label for="name">Nom</label>
            <input type="text" name="name" id="name"
                   value="<?= htmlspecialchars($old['name'] ?? $user['name'] ?? '', ENT_QUOTES) ?>"
                   required>
            <?php if (!empty($errors['name'])): ?>
                <p class="field-error"><?= htmlspecialchars($errors['name'], ENT_QUOTES) ?></p>
            <?php endif; ?>
        </div>

        <div class="field-excerpt">
            <label for="bio">Biografia <small>(opcional)</small></label>
            <textarea name="bio" id="bio" rows="3"
                      placeholder="Explica alguna cosa sobre tu..."><?= htmlspecialchars($old['bio'] ?? $user['bio'] ?? '', ENT_QUOTES) ?></textarea>
        </div>

        <div class="field-title">
            <label for="password">Nova contrasenya <small>(deixa buit per no canviar)</small></label>
            <input type="password" name="password" id="password"
                   placeholder="Mínim 8 caràcters, 1 majúscula, 1 número, 1 símbol">
            <?php if (!empty($errors['password'])): ?>
                <p class="field-error"><?= htmlspecialchars($errors['password'], ENT_QUOTES) ?></p>
            <?php endif; ?>
        </div>

        <div class="field-title">
            <label for="password_confirmation">Confirmar contrasenya</label>
            <input type="password" name="password_confirmation" id="password_confirmation">
            <?php if (!empty($errors['password_confirmation'])): ?>
                <p class="field-error"><?= htmlspecialchars($errors['password_confirmation'], ENT_QUOTES) ?></p>
            <?php endif; ?>
        </div>

        <div class="field-actions">
            <button type="submit" class="btn btn-primary">Desar canvis</button>
            <a href="<?= BASE_PATH ?>/profile" class="btn btn-outline-secondary">Cancel·lar</a>
        </div>
    </form>

    <!-- Formulari d'avatar separat -->
    <form method="POST" action="<?= BASE_PATH ?>/profile/avatar"
          enctype="multipart/form-data" class="form-grid-post" style="margin-top: 2rem;">
        <?= \App\Infrastructure\Security\Csrf::field() ?>

        <div class="mTitle">Actualitzar avatar</div>

        <div class="profile-avatar-preview">
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?= BASE_PATH ?>/storage/uploads/<?= htmlspecialchars($user['avatar'], ENT_QUOTES) ?>"
                     alt="Avatar actual">
            <?php else: ?>
                <div class="avatar-placeholder">
                    <?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="field-title">
            <label for="avatar">Imatge <small>(JPG, PNG, GIF, WEBP — màx. 2 MB)</small></label>
            <input type="file" name="avatar" id="avatar" accept="image/*">
        </div>

        <div class="field-actions">
            <button type="submit" class="btn btn-primary">Pujar avatar</button>
        </div>
    </form>

</section>