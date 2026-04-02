<?php
	session_start();
    $_SESSION["erreur"]=null; 
	try{
		require_once("db.php");
		$db_request = $db_connection->prepare(
			"SELECT id, pseudo, email, `password`, `role` FROM user WHERE email = :email"
		);
		$db_request->execute([
			":email" => $_POST["email"],
		]);
		$user = $db_request->fetch();
		if (password_verify($_POST["mdp"], $user["password"])) {
			$_SESSION["id_user"] = $user["id"];
			$_SESSION["pseudo"] = $user["pseudo"];
			$_SESSION["email"] = $user["email"];
			$_SESSION["role"] = $user["role"];

			header("Location: index.php");
			exit();
		}
	} catch (Exception $e) {
		header("Location: login.php");
        $_SESSION["erreur"] = $e;
		exit();
	}
?>