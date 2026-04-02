<?php
require_once "db.php";
require_once "Critique_accueil.php";
session_start();

$critique = new Critique($db_connection);

$all = $critique->getAll();

$featured = array_slice($all, 0, 5);

$epingles = array_filter($all, fn($c) => $c['epingle'] == 1);

$images = ["a.jpg","b.jpg","c.jpg","d.jpg","e.jpg","f.jpg","g.jpg"];
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<?php require_once('navbar.php'); ?>

<div class="container">


<h1> Films en avant</h1>
<div class="featured">

<?php $i=0; foreach($epingles as $c): ?>
<?php $img = $images[$i % count($images)]; $i++; ?>

<div class="featured-card">
    <img src="<?= $img ?>">

    <div class="overlay">
        <h2><?= $c['titre'] ?></h2>
        <p>⭐ <?= $c['note'] ?>/10</p>
        <a href="critique.php?id=<?= $c['id_critique'] ?>">Voir</a>
    </div>
</div>

<?php endforeach; ?>

</div>

<h1> Critiques</h1>
<div class="grid">

<?php $i=0; foreach($all as $c): ?>
<?php $img = $images[$i % count($images)]; $i++; ?>

<div class="card">
    <img src="<?= $img ?>">

    <h3>
        <a href="critique.php?id=<?= $c['id_critique'] ?>">
            <?= $c['titre'] ?>
        </a>
    </h3>

    <p><?= substr($c['contenu'],0,100) ?>...</p>
    <p>⭐ <?= $c['note'] ?>/10</p>
</div>

<?php endforeach; ?>

</div>

</div>

</div>

</body>
</html>