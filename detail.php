<?php
require_once "db.php";
require_once "Critique.php";

$db = (new Database())->connect();
$critique = new Critique($db);

$id = $_GET['id'];

$all = $critique->getAll();

$film = null;

foreach($all as $c) {
    if($c['id'] == $id) {
        $film = $c;
    }
}

$images = ["a.jpg","b.jpg","c.jpg","d.jpg","e.jpg","f.jpg","g.jpg"];
$img = $images[$film['id'] % count($images)];
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
</head>
<body>

<a href="index.php">⬅ Retour</a>

<div class="container">

<img src="<?= $img ?>" style="width:100%; max-height:400px; object-fit:cover;">

<h1><?= $film['titre'] ?></h1>

<p><?= $film['contenu'] ?></p>

<p>⭐ <?= $film['note'] ?>/5</p>

</div>

</body>
</html>