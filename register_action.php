<?php
require_once "db.php";

$pseudo = $_POST['pseudo'];
$email = $_POST['email'];
$password = $_POST['password'];

$sql = "INSERT INTO user(pseudo, email, password, role) VALUES(?,?,?,?)";
$req = $db_connection->prepare($sql);
$req->execute([$pseudo, $email, $password, 'utilisateur']);

header("Location: login.php");