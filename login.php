<?php
	session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Identification</title>
	</head>
	<body>
		<?php require_once("navbar.php"); ?>
		<main>
			<article>
				<h1>Login</h1>
				<form action="authenticate.php" method="POST">
					<input placeholder="Pseudo" name="pseudo"><br>
					<input placeholder="Mot de passe" name="mdp"><br>
					<input class="submit-button" type="submit">
				</form>
                <?php if ($_SESSION["erreur"]==true){ ?>
                    <p class="erreur">Mauvais mot de passe</p> 
                <?php }?>
			</article>
		</main>
	</body>
	
</html>