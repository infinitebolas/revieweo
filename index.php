<?php
session_start();

require_once "db.php";
require_once "Critique.php";

$db = (new Database())->connect();
$critique = new Critique($db);

$critiques = $critique->getAll();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h2>🎬 Revieweo</h2>

    <div>
        <?php if(isset($_SESSION['user'])): ?>
            <?= $_SESSION['user']['pseudo'] ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</header>

<div class="container">

<h1>Critiques de films</h1>

<?php foreach($critiques as $c): ?>

<div class="card">
    <h3><?= $c['titre'] ?></h3>
    <p><?= substr($c['contenu'], 0, 150) ?>...</p>
    <p>⭐ <?= $c['note'] ?>/5</p>
    <p>👤 <?= $c['pseudo'] ?></p>

    <?php if(isset($_SESSION['user'])): ?>
        <a href="like.php?id=<?= $c['id'] ?>">❤️ Like</a>
    <?php endif; ?>
</div>

<?php endforeach; ?>

</div>
</body>
</html>