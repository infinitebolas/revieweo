<?php
session_start();
require_once "db.php";

if(!isset($_SESSION['user'])) {
    die("Non connecté");
}

$id = $_SESSION['user']['id'];

$sql = "SELECT * FROM critique WHERE id_user=?";
$req = $db_connection->prepare($sql);
$req->execute([$id]);

$critiques = $req->fetchAll();
?>

<h2>Mes critiques</h2>

<a href="add_critique.php">Ajouter une critique</a>
<br><br>

<?php foreach($critiques as $c): ?>
    <hr>
    <h3><?= $c['titre'] ?></h3>
    <p><?= $c['contenu'] ?></p>
    <p>Note : <?= $c['note'] ?></p>
<?php endforeach; ?>

<br>
<a href="index.php">Retour accueil</a>