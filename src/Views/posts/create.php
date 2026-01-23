<?php
// views/tasques/create.php

// Mostrar errores si existen
if (!empty($errors)) {
    echo '<div class="form-error">';
    foreach ($errors as $fieldErrors) {
        foreach ($fieldErrors as $error) {
            echo "<p>{$error}</p>";
        }
    }
    echo '</div>';
}

// Valores previos del formulario
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$tags = $_POST['tags'] ?? '[]';
$cost = $_POST['cost'] ?? 0;
$due_date = $_POST['due_date'] ?? date('Y-m-d\TH:i');
$expected_hours = $_POST['expected_hours'] ?? 20;
$used_hours = $_POST['used_hours'] ?? 0;
$priority = $_POST['priority'] ?? 'medium';
$state = $_POST['state'] ?? 'pending';
?>

<form action="<?= BASE_PATH ?>/tasques" method="POST" class="form-grid">
    <div class="mTitle">Crear nova tasca</div>

    <div class="title">
        <label>Títol</label>
        <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" placeholder="Entra el teu titol aqui..." required> 
    </div>

    <div class="priority">
        <label>Prioritat</label>
        <select name="priority">
            <option value="low" <?= $priority === 'low' ? 'selected' : '' ?>>Baixa</option>
            <option value="medium" <?= $priority === 'medium' ? 'selected' : '' ?>>Mitjana</option>
            <option value="high" <?= $priority === 'high' ? 'selected' : '' ?>>Alta</option>
        </select>
    </div>

    <div class="tags">
        <label>Etiquetes (tag1,tag2,tag3)</label>
        <input type="text" name="tags" value="<?= htmlspecialchars($tags) == '[]' ? '' : htmlspecialchars($tags) ?>" placeholder="Entra les teves etiquetes aqui...">
    </div>

    <div class="description">
        <label>Descripció</label>
        <textarea name="description" placeholder="Entra la teva descripció aqui..."><?= htmlspecialchars($description) ?></textarea>
    </div>

    <div class="state">
        <label>Estat</label>
        <select name="state">
            <option value="pending" <?= $state === 'pending' ? 'selected' : '' ?>>Pendent</option>
            <option value="in-progress" <?= $state === 'in-progress' ? 'selected' : '' ?>>En progrés</option>
            <option value="blocked" <?= $state === 'blocked' ? 'selected' : '' ?>>Bloquejada</option>
            <option value="completed" <?= $state === 'completed' ? 'selected' : '' ?>>Completada</option>
        </select>
    </div>

    <div class="cost">
        <label>Cost</label>
        <input type="number" step="0.01" name="cost" value="<?= htmlspecialchars($cost) ?>">
    </div>

    <div class="due_date">
        <label>Data límit</label>
        <input type="datetime-local" name="due_date" value="<?= htmlspecialchars($due_date) ?>">
    </div>

    <div class="expected_hours">
        <label>Hores esperades</label>
        <input type="number" name="expected_hours" value="<?= htmlspecialchars($expected_hours) ?>">
    </div>

    <div class="used_hours">
        <label>Hores utilitzades</label>
        <input type="number" name="used_hours" value="<?= htmlspecialchars($used_hours) ?>">
    </div>

    <div class="actions">
        <button type="submit" class="btn btn-primary">Crear</button>
        <a href="<?= BASE_PATH ?>/tasques" class="btn btn-outline-secondary">Cancel·lar</a>
    </div>
</form>