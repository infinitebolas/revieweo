<?php
require_once "db.php";

$pseudo = $_POST['pseudo'];
$email = $_POST['email'];
$password = $_POST['password'];

$sql = "INSERT INTO user(pseudo, email, password) VALUES(?,?,?)";
$req = $db_connection->prepare($sql);
$req->execute([$pseudo, $email, $password]);

echo "Utilisateur créé <br>";
echo "<a href='login.php'>Se connecter</a>";