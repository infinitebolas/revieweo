<?php
session_start();
require_once "db.php";

if(!isset($_SESSION['user'])) {
    die("Non connecté");
}

$titre = $_POST['titre'];
$contenu = $_POST['contenu'];
$note = $_POST['note'];
$id_user = $_SESSION['user']['id'];

$sql = "INSERT INTO critique(titre, contenu, note, id_user) VALUES(?,?,?,?)";
$req = $db_connection->prepare($sql);
$req->execute([$titre, $contenu, $note, $id_user]);

header("Location: dashboard.php");