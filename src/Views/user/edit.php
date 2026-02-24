<?php
// views/user/edit.php

/** @var array $user   */
/** @var array $errors */
/** @var array $old    */

$errors = $errors ?? [];
$old    = $old    ?? [];
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7 d-flex flex-column gap-4">

        <!-- Formulari perfil -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <h1 class="h5 fw-bold mb-4">Editar perfil</h1>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_PATH ?>/profile/update">
                    <?= \App\Infrastructure\Security\Csrf::field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nom</label>
                        <input type="text" name="name" id="name"
                               class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($old['name'] ?? $user['name'] ?? '', ENT_QUOTES) ?>"
                               required>
                        <?php if (!empty($errors['name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['name'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label fw-semibold">
                            Biografia <span class="text-muted fw-normal small">(opcional)</span>
                        </label>
                        <textarea name="bio" id="bio" class="form-control" rows="3"
                                  placeholder="Explica alguna cosa sobre tu..."><?= htmlspecialchars($old['bio'] ?? $user['bio'] ?? '', ENT_QUOTES) ?></textarea>
                    </div>

                    <hr class="my-4">

                    <p class="fw-semibold mb-3">Canviar contrasenya <span class="text-muted fw-normal small">(deixa buit per no canviar)</span></p>

                    <div class="mb-3">
                        <label for="password" class="form-label">Nova contrasenya</label>
                        <input type="password" name="password" id="password"
                               class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                               placeholder="Mínim 8 caràcters, 1 majúscula, 1 número, 1 símbol">
                        <?php if (!empty($errors['password'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['password'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirmar contrasenya</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-control <?= !empty($errors['password_confirmation']) ? 'is-invalid' : '' ?>"
                               placeholder="Repeteix la nova contrasenya">
                        <?php if (!empty($errors['password_confirmation'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['password_confirmation'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?= BASE_PATH ?>/profile" class="btn btn-outline-secondary">Cancel·lar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-floppy me-1"></i>Desar canvis
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- Formulari avatar -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <h2 class="h5 fw-bold mb-4">Actualitzar avatar</h2>

                <div class="d-flex align-items-center gap-4 mb-4">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= BASE_PATH ?>/storage/uploads/<?= htmlspecialchars($user['avatar'], ENT_QUOTES) ?>"
                             alt="Avatar actual" class="avatar-lg">
                    <?php else: ?>
                        <div class="avatar-placeholder-lg">
                            <?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <p class="text-muted small mb-0">
                        Formats acceptats: JPG, PNG, WEBP<br>
                        Mida màxima: 2 MB
                    </p>
                </div>

                <form method="POST" action="<?= BASE_PATH ?>/profile/avatar"
                      enctype="multipart/form-data">
                    <?= \App\Infrastructure\Security\Csrf::field() ?>

                    <div class="mb-3">
                        <label for="avatar" class="form-label fw-semibold">Selecciona una imatge</label>
                        <input type="file" name="avatar" id="avatar"
                               class="form-control" accept="image/*">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-1"></i>Pujar avatar
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>