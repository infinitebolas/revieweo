<?php
session_start();
require_once 'db.php';

// 🔒 Vérification du rôle
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['critique', 'administrateur'])) {
    header('Location: index.php');
    exit();
}

// 🔐 Sécurisation de l'ID
$id_critique = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id_critique <= 0) {
    die("ID invalide.");
}

$message = "";

// 🟢 MODIFICATION
if (isset($_POST['modifier'])) {

    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $note = (int) ($_POST['note'] ?? 0);
    $id_cat = (int) ($_POST['id_categorie'] ?? 0);

    if ($titre && $contenu && $note >= 0 && $note <= 10) {

        $stmt = $db_connection->prepare("
            UPDATE critique 
            SET titre = ?, contenu = ?, note = ?, id_categorie = ?
            WHERE id_critique = ? 
            AND (id_user = ? OR ? = 'administrateur')
        ");

        $success = $stmt->execute([
            $titre,
            $contenu,
            $note,
            $id_cat,
            $id_critique,
            $_SESSION['id_user'],
            $_SESSION['role']
        ]);

        if ($success) {
            header("Location: index.php");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>❌ Erreur lors de la modification</div>";
        }

    } else {
        $message = "<div class='alert alert-danger'>❌ Données invalides</div>";
    }
}

// 📥 Récupération de la critique sécurisée
$stmt = $db_connection->prepare("
    SELECT * FROM critique 
    WHERE id_critique = ? 
    AND (id_user = ? OR ? = 'administrateur')
");

$stmt->execute([
    $id_critique,
    $_SESSION['id_user'],
    $_SESSION['role']
]);

$critique = $stmt->fetch();

if (!$critique) {
    die("Critique introuvable ou accès interdit.");
}

// 📂 Catégories
$categories = $db_connection->query("SELECT * FROM categorie")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une critique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php require_once('navbar.php');?>
<div class="container py-5">
    <div class="col-md-8 mx-auto">

        <?= $message ?>

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                Modifier la critique #<?= htmlspecialchars($id_critique) ?>
            </div>

            <div class="card-body">
                <form method="POST">

                    <div class="mb-3">
                        <label>Titre</label>
                        <input type="text" name="titre" class="form-control"
                               value="<?= htmlspecialchars($critique['titre']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Contenu</label>
                        <textarea name="contenu" class="form-control" rows="5" required><?= htmlspecialchars($critique['contenu']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Note</label>
                        <input type="number" name="note" min="0" max="10"
                               value="<?= $critique['note'] ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Catégorie</label>
                        <select name="id_categorie" class="form-select">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                    <?= $cat['id'] == $critique['id_categorie'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="submit" name="modifier" class="btn btn-success">
                                Enregistrer
                            </button>
                            <a href="index.php" class="btn btn-secondary">Annuler</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

</body>
</html>