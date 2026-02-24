<?php
// views/posts/create.php

/** @var array $errors */
/** @var array $old    */

$errors = $errors ?? [];
$old    = $old    ?? [];
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">

        <h1 class="h4 fw-bold mb-4">Nou post</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <div><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_PATH ?>/my-posts">
            <?= \App\Infrastructure\Security\Csrf::field() ?>

            <div class="mb-3">
                <label for="title" class="form-label fw-semibold">Títol</label>
                <input type="text" name="title" id="title"
                       class="form-control <?= !empty($errors['title']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES) ?>"
                       placeholder="Títol del post..."
                       required>
                <?php if (!empty($errors['title'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['title'], ENT_QUOTES) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label fw-semibold">Estat</label>
                <select name="status" id="status" class="form-select">
                    <?php foreach (['draft' => 'Esborrany', 'published' => 'Publicat', 'archived' => 'Arxivat'] as $val => $label): ?>
                        <option value="<?= $val ?>"
                            <?= ($old['status'] ?? 'draft') === $val ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="excerpt" class="form-label fw-semibold">
                    Resum <span class="text-muted fw-normal small">(opcional, es genera automàticament)</span>
                </label>
                <textarea name="excerpt" id="excerpt" class="form-control" rows="2"
                          placeholder="Resum breu del post..."><?= htmlspecialchars($old['excerpt'] ?? '', ENT_QUOTES) ?></textarea>
            </div>

            <div class="mb-4">
                <label for="content" class="form-label fw-semibold">Contingut</label>
                <textarea name="content" id="content"
                          class="form-control <?= !empty($errors['content']) ? 'is-invalid' : '' ?>"
                          rows="14"
                          placeholder="Escriu el contingut del post aquí..."
                          required><?= htmlspecialchars($old['content'] ?? '', ENT_QUOTES) ?></textarea>
                <?php if (!empty($errors['content'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['content'], ENT_QUOTES) ?></div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="<?= BASE_PATH ?>/my-posts" class="btn btn-outline-secondary">Cancel·lar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>Crear post
                </button>
            </div>

        </form>
    </div>
</div>