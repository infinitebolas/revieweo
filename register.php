<?php
	session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Enregistrement</title>
	</head>
	<body>
		<?php require_once("navbar.php"); ?>
		<main>
			<article>
			<h2>S'Enregistrer</h2>
			<form action="registration.php" method="POST">
					<input placeholder="Pseudo" name="pseudo"><br>
					<input placeholder="Email" name="email"><br>
					<input placeholder="Mot de passe" name="mdp"><br>
					<input class="submit-button" type="submit">
				</form>
                <?php if ($_SESSION["erreur"]==true){ ?>
                    <p class="erreur"> Merci de remplir tous les champs </p> 
                <?php }?>
			</article>
		</main>
	</body>
	
</html>