<?php
// views/tasques/edit.php

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

// Valores previos del formulario (POST > modelo)
// Titol
$title = $_POST['title'] 
    ?? $task->title;

// Descripcio
$description = $_POST['description'] 
    ?? $task->description;

// Tag Array
$tagsArray = $_POST['tags'] 
    ?? (is_string($task->tags)
        ? json_decode($task->tags, true)
        : ($task->tags ?? [])
    );

// Tags
$tags = empty($tagsArray)
    ? ''
    : implode(', ', $tagsArray);

// Cost
$cost = $_POST['cost'] 
    ?? $task->cost;
    
// Data limit
$due_date = $_POST['due_date'] 
    ?? $task->due_date;

// Hores esperades
$expected_hours = $_POST['expected_hours'] 
    ?? $task->expected_hours;

// Hores fetes servir
$used_hours = $_POST['used_hours'] 
    ?? $task->used_hours;

// prioritat
$priority = $_POST['priority'] 
    ?? $task->priority;

// Estat
$state = $_POST['state'] 
    ?? $task->state;
?>

<form action="<?= BASE_PATH ?>/tasques/<?= $task->id ?>" method="POST" class="form-grid">
    <div class="mTitle">Editar tasca</div>

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
        <input type="text" name="tags" value="<?= htmlspecialchars($tags) == '[]' ? : htmlspecialchars($tags) ?>"  placeholder="Entra les teves etiquetes aqui...">
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
        <input
            type="datetime-local"
            name="due_date"
            value="<?= $due_date ? date('Y-m-d\TH:i', strtotime($due_date)) : '' ?>"
        >
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
        <button type="submit" class="btn btn-primary">Desar canvis</button>
        <a href="<?= BASE_PATH ?>/tasques" class="btn btn-outline-secondary">Cancel·lar</a>
    </div>
</form>