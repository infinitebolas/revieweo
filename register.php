<?php
	session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Enregistrement</title>
        <link rel="stylesheet" href="style.css">
	</head>
	<body>
		<?php require_once("navbar.php"); ?>
		<main>
			<article>
			<h1>S'Enregistrer</h1>
			<form action="registration.php" method="POST">
					<input placeholder="Pseudo" name="pseudo"><br>
					<input type="email" placeholder="Email" name="email"><br>
					<input type="password" placeholder="Mot de passe" name="mdp"><br>
					<select name="role">
						<option>Utilisateur</option>
						<option>Critique</option>
					</select>
					<input class="submit-button" type="submit">
				</form>
                <?php 
                if (isset($_SESSION["doublon"])){ ?>
                    <p class="erreur"> Cet email existe déjà </p> 
                    
                <?php 
                    $_SESSION["doublon"]=null; 
                }?>
			</article>
		</main>
	</body>
	
</html>