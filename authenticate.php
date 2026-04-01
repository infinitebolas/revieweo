<?php
	session_start();
    $_SESSION["erreur"]=null; 
	require_once("db.php");
	$db_request = $db_connection->prepare(
		"SELECT id, pseudo, email, password, role FROM user WHERE pseudo = :pseudo"
	);
	$db_request->execute([
		":pseudo" => $_POST["pseudo"],
	]);
	$user = $db_request->fetch();
	if (password_verify($_POST["mdp"], $user["password"])) {
		$_SESSION["id"] = $user["id"];
		$_SESSION["pseudo"] = $user["pseudo"];
		$_SESSION["email"] = $user["email"];
		$_SESSION["mdp"] = $user["password"];
		$_SESSION["role"] = $user["role"];

		header("Location: index.php");
		exit();
	} else {
		header("Location: login.php");
        $_SESSION["erreur"]=true;   
		exit();
	}
?>