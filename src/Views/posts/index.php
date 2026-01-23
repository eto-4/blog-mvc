<?php
// views/tasques/index.php
require_once APP_ROOT . '/helpers/FlashMessages.php';
?>

<section class="tasks">
    <header class="tasks-header">
        <h1>Llistat de tasques</h1>

        <a href="<?= BASE_PATH ?>/tasques/create" class="btn btn-success">
            Crear nova tasca
        </a>
    </header>

    <?php FlashMessages::display(); ?>

    <?php if (!empty($tasks)): ?>
        <div class="table-wrapper">
            <table class="tasks-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Títol</th>
                        <th>Descripció</th>
                        <th>Cost (€)</th>
                        <th>Estat</th>
                        <th>Progrés</th>
                        <th>Data d'entrega</th>
                        <th>Prioritat</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= $task->id ?></td>
                            <td><?= htmlspecialchars($task->title) ?></td>
                            <td><?= htmlspecialchars($task->description) ?></td>
                            <td><?= htmlspecialchars($task->cost) ?> €</td>
                            <td><?= htmlspecialchars($task->state) ?></td>
                            <td><?= $task->used_hours ?>/<?= $task->expected_hours ?> h</td>
                            <td><?= htmlspecialchars($task->due_date) ?></td>
                            <td class="priority <?= $task->priority ?>">
                                <?= ucfirst($task->priority) ?>
                            </td>
                            <td class="actions">
                                <a href="<?= BASE_PATH ?>/tasques/<?= $task->id ?>/edit" class="btn btn-primary btn-sm">
                                    Editar
                                </a>

                                <form action="<?= BASE_PATH ?>/tasques/<?= $task->id ?>/delete"
                                      method="POST"
                                      onsubmit="return confirm('Segur que vols eliminar aquesta tasca?');">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="empty-state">No hi ha tasques disponibles.</p>
    <?php endif; ?>
</section>