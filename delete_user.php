<?php
require_once("db.php");

$pseudo = $_POST['pseudo'];

$supp = $db_connection->prepare("DELETE FROM user WHERE pseudo = :pseudo");
$result = $supp->execute(['pseudo' => $pseudo]);

if ($result) {
    echo json_encode(["success" => true]);
}