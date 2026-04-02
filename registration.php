<?php
	session_start();
    $_SESSION["doublon"]=null; 
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
                $_SESSION["doublon"]=true;
                return false;
			}
			return true;
		}

		if (check_email($db_connection,$_POST["email"]) and $_POST["pseudo"]!=null and $_POST["mdp"]!=null) {
			$db_request = $db_connection->prepare(
				"INSERT INTO user (pseudo, email, password, role) VALUES
				(:pseudo, :email, :mdp, :role)"
			);
			$db_request->execute([
				":pseudo" => $_POST["pseudo"],
				":email" => $_POST["email"],
				":mdp" => password_hash($_POST["mdp"], PASSWORD_DEFAULT),
				":role" => $_POST["role"]
			]);        
            header('Location: login.php');
            exit();
		}  
   
        header('Location: register.php');
        exit();
?>
