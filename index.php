<?php
session_start();
require_once "db.php";

$sql = "SELECT critique.*, user.pseudo 
        FROM critique 
        JOIN user ON critique.id_user = user.id 
        ORDER BY date_creation DESC";

$req = $db_connection->query($sql);
$critiques = $req->fetchAll();
?>

<h2>Liste des critiques</h2>

<?php foreach($critiques as $c): ?>

    <hr>
    <h3><?= $c['titre'] ?></h3>
    <p><?= $c['contenu'] ?></p>
    <p>Note : <?= $c['note'] ?>/5</p>
    <p>Auteur : <?= $c['pseudo'] ?></p>

<?php endforeach; ?>

<hr>

<?php if(isset($_SESSION['user'])): ?>
    <p>Connecté : <?= $_SESSION['user']['pseudo'] ?></p>
    <a href="dashboard.php">Dashboard</a><br>
    <a href="logout.php">Logout</a>
<?php else: ?>
    <a href="login.php">Login</a><br>
    <a href="register.php">Register</a>
<?php endif; ?>