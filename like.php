<?php
session_start();

require_once "db.php";

$db = (new Database())->connect();

if(!isset($_SESSION['user'])) {
    die("Non connecté");
}

$id_user = $_SESSION['user']['id'];
$id_critique = $_GET['id'];

$sql = "SELECT * FROM `like` WHERE id_user=? AND id_critique=?";
$req = $db->prepare($sql);
$req->execute([$id_user, $id_critique]);

if($req->rowCount() == 0) {
    $insert = $db->prepare("INSERT INTO `like` (id_user, id_critique) VALUES (?, ?)");
    $insert->execute([$id_user, $id_critique]);
}

header("Location: ../index.php");