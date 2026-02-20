<?php
// views/posts/edit.php

/** @var App\Domain\Models\Post $post */
/** @var array                  $errors */
/** @var array                  $old    */
/** @var string                 $csrfToken */

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

    <form method="POST"
          action="<?= BASE_PATH ?>/my-posts/<?= (int) $post->id ?>/update"
          class="form-grid-post">

        <?= \App\Infrastructure\Security\Csrf::field() ?>

        <div class="mTitle">Editar post</div>

        <div class="field-title">
            <label for="title">Títol</label>
            <input type="text" name="title" id="title"
                   value="<?= htmlspecialchars($old['title'] ?? $post->title ?? '', ENT_QUOTES) ?>"
                   placeholder="Títol del post..."
                   required>
            <?php if (!empty($errors['title'])): ?>
                <p class="field-error"><?= htmlspecialchars($errors['title'], ENT_QUOTES) ?></p>
            <?php endif; ?>
        </div>

        <div class="field-status">
            <label for="status">Estat</label>
            <select name="status" id="status">
                <?php
                $currentStatus = $old['status'] ?? $post->status ?? 'draft';
                foreach (['draft' => 'Esborrany', 'published' => 'Publicat', 'archived' => 'Arxivat'] as $val => $label):
                ?>
                    <option value="<?= $val ?>" <?= $currentStatus === $val ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field-excerpt">
            <label for="excerpt">Resum <small>(opcional)</small></label>
            <textarea name="excerpt" id="excerpt" rows="2"
                      placeholder="Resum breu del post..."><?= htmlspecialchars($old['excerpt'] ?? $post->excerpt ?? '', ENT_QUOTES) ?></textarea>
        </div>

        <div class="field-content">
            <label for="content">Contingut</label>
            <textarea name="content" id="content" rows="12"
                      placeholder="Escriu el contingut del post aquí..."
                      required><?= htmlspecialchars($old['content'] ?? $post->content ?? '', ENT_QUOTES) ?></textarea>
            <?php if (!empty($errors['content'])): ?>
                <p class="field-error"><?= htmlspecialchars($errors['content'], ENT_QUOTES) ?></p>
            <?php endif; ?>
        </div>

        <div class="field-actions">
            <button type="submit" class="btn btn-primary">Desar canvis</button>
            <a href="<?= BASE_PATH ?>/my-posts" class="btn btn-outline-secondary">Cancel·lar</a>
        </div>

    </form>

</section>