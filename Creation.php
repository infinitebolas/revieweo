<?php
require_once("db.php"); // $db_connection est un objet PDO
session_start();

// Récupération des catégories
$stmt = $db_connection->query("SELECT id, nom FROM categorie ORDER BY nom ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une Critique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once('navbar.php');?>
<div class="container mt-5">
    <h1 class="text-center mb-4">Créer une Critique</h1>

    <div id="message"></div>

    <form method="POST" action="create_critique.php" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" required>
        </div>

        <div class="mb-3">
            <label for="contenu" class="form-label">Contenu</label>
            <textarea class="form-control" id="contenu" name="contenu" rows="5" required></textarea>
        </div>

        <div class="mb-3">
            <label for="note" class="form-label">Note (0-10)</label>
            <input type="number" class="form-control" id="note" name="note" min="0" max="10" required>
        </div>

        <div class="mb-3">
            <label for="categorie_id" class="form-label">Catégorie</label>
            <select class="form-select" id="categorie_id" name="categorie_id" required>
                <option value="">-- Sélectionnez une catégorie --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Ajouter la Critique</button>
    </form>
</div>
</body>
</html>