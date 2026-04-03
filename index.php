<?php
require_once "db.php";
require_once "Critique_accueil.php";
session_start();

$critique = new Critique($db_connection);

// Récupération des données
$all = $critique->getAll();
$featured = array_slice($all, 0, 5);
$epingles = array_filter($all, fn($c) => $c['epingle'] == 1);

// Mapping catégorie → image
$images = [
    "science-fiction" => "sf.jpg",
    "aventure" => "aventure.jpg",
    "comedie" => "comedie.jpg",
    "horreur" => "horreur.jpg",
    "fantastique" => "fantastique.jpg",
    "romance" => "romance.jpg"
];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php require_once('navbar.php'); ?>

<div class="container">

    <h1>Films en avant</h1>
    <div class="featured">

        <?php foreach($epingles as $c): ?>

            <?php 
            $categorie = strtolower(trim($c['categorie']));
            $img = $images[$categorie] ?? "default.jpg";
            ?>

            <div class="featured-card">
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($c['categorie']) ?>">

                <div class="overlay">
                    <h2><?= htmlspecialchars($c['titre']) ?></h2>
                    <p>⭐ <?= $c['note'] ?>/10</p>
                    <a href="critique.php?id=<?= $c['id_critique'] ?>">Voir</a>
                </div>
            </div>

        <?php endforeach; ?>

    </div>

    <h1>Critiques</h1>
    <div class="grid">

        <?php foreach($all as $c): ?>

            <?php 
            $categorie = strtolower(trim($c['categorie']));
            $img = $images[$categorie] ?? "default.jpg";
            ?>

            <div class="card">
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($c['categorie']) ?>">

                <h3>
                    <a href="critique.php?id=<?= $c['id_critique'] ?>">
                        <?= htmlspecialchars($c['titre']) ?>
                    </a>
                </h3>

                <p><?= htmlspecialchars(substr($c['contenu'], 0, 100)) ?>...</p>
                <p>⭐ <?= $c['note'] ?>/10</p>
            </div>

        <?php endforeach; ?>

    </div>

</div>

</body>
</html>