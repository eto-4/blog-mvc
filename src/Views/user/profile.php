<?php
// views/user/profile.php

/** @var array $user  */
/** @var array $stats */
?>

<section class="profile-section">

    <div class="profile-header">
        <div class="profile-avatar">
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?= BASE_PATH ?>/storage/uploads/<?= htmlspecialchars($user['avatar'], ENT_QUOTES) ?>"
                     alt="Avatar de <?= htmlspecialchars($user['name'], ENT_QUOTES) ?>">
            <?php else: ?>
                <div class="avatar-placeholder">
                    <?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="profile-info">
            <h1><?= htmlspecialchars($user['name'], ENT_QUOTES) ?></h1>
            <p class="profile-email"><?= htmlspecialchars($user['email'], ENT_QUOTES) ?></p>
            <?php if (!empty($user['bio'])): ?>
                <p class="profile-bio"><?= htmlspecialchars($user['bio'], ENT_QUOTES) ?></p>
            <?php endif; ?>
            <p class="profile-since">
                Membre des de <?= date('d/m/Y', strtotime($user['created_at'])) ?>
            </p>
        </div>

        <div class="profile-actions">
            <a href="<?= BASE_PATH ?>/profile/edit" class="btn btn-primary">Editar perfil</a>
        </div>
    </div>

    <div class="profile-stats">
        <div class="stat-card">
            <span class="stat-number"><?= $stats['total_posts'] ?></span>
            <span class="stat-label">Posts totals</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?= $stats['published_posts'] ?></span>
            <span class="stat-label">Publicats</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?= $stats['total_views'] ?></span>
            <span class="stat-label">Visualitzacions</span>
        </div>
    </div>

    <div class="profile-links">
        <a href="<?= BASE_PATH ?>/my-posts" class="btn btn-primary">Veure els meus posts</a>
        <a href="<?= BASE_PATH ?>/author/<?= (int) $user['id'] ?>" class="btn btn-outline-secondary">
            Perfil públic
        </a>
    </div>

</section>