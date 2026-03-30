<?php
	session_start();
    $_SESSION["erreur"]=false; 
	require_once("db.php");
		function check_email($db_connection, $email) {
			$db_request = $db_connection->prepare(
				"SELECT email FROM user
				WHERE email=:email"
			);
			$db_request->execute([
				":email" => $email,
			]);
			if ($db_request->rowCount()) {
                echo ('Cet utilisateur existe déjà !');
                return false;
			}
			return true;
		}

		if (check_email($db_connection,$_POST["email"]) and $_POST["pseudo"]!=null and $_POST["mdp"]!=null) {
			$db_request = $db_connection->prepare(
				"INSERT INTO user (pseudo, email, password) VALUES
				(:pseudo, :email, :mdp)"
			);
			$db_request->execute([
				":pseudo" => $_POST["pseudo"],
				":email" => $_POST["email"],
				":mdp" => password_hash($_POST["mdp"], PASSWORD_DEFAULT),
			]);        
            header('Location: login.php');
            exit();
		}  
   
        header('Location: register.php');
        $_SESSION["erreur"]=true;   
        exit();
?>
