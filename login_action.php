<?php
session_start();
require_once "db.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM user WHERE email=?";
$req = $db_connection->prepare($sql);
$req->execute([$email]);

$user = $req->fetch();

if($user && $user['password'] == $password) {
    $_SESSION['user'] = $user;
    header("Location: index.php");
} else {
    echo "Erreur login";
}