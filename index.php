<?php
require_once "db.php";
require_once "Critique.php";

$db = (new Database())->connect();
$critique = new Critique($db);

$all = $critique->getAll();

$featured = array_slice($all, 0, 5);

$epingles = array_filter($all, fn($c) => $c['epingle'] == 1);

$images = ["a.jpg","b.jpg","c.jpg","d.jpg","e.jpg","f.jpg","g.jpg"];
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h2>Revieweo</h2>
</header>

<div class="container">


<h1> Films en avant</h1>
<div class="featured">

<?php $i=0; foreach($featured as $c): ?>
<?php $img = $images[$i % count($images)]; $i++; ?>

<div class="featured-card">
    <img src="<?= $img ?>">

    <div class="overlay">
        <h2><?= $c['titre'] ?></h2>
        <p>⭐ <?= $c['note'] ?>/5</p>
        <a href="detail.php?id=<?= $c['id'] ?>">Voir</a>
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
        <a href="detail.php?id=<?= $c['id'] ?>">
            <?= $c['titre'] ?>
        </a>
    </h3>

    <p><?= substr($c['contenu'],0,100) ?>...</p>
    <p>⭐ <?= $c['note'] ?>/5</p>
</div>

<?php endforeach; ?>

</div>

<h1> Films épinglés</h1>
<div class="slider">

<?php $i=0; foreach($epingles as $c): ?>
<?php $img = $images[$i % count($images)]; $i++; ?>

<div class="card">
    <img src="<?= $img ?>">
    <h3><?= $c['titre'] ?></h3>
</div>

<?php endforeach; ?>

</div>

</div>

</body>
</html>