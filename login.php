<?php
	session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Identification</title>
        <link rel="stylesheet" href="style.css">
	</head>
	<body>
		<?php require_once("navbar.php"); ?>
		<main>
			<article>
				<h1>Login</h1>
				<form action="authenticate.php" method="POST">
					<input type="email" placeholder="Email" name="email" required><br>
					<input type="password" placeholder="Mot de passe" name="mdp" required><br>
					<input class="submit-button" type="submit">
				</form>
                <?php if (isset($_SESSION["erreur"])){ ?>
                    <p class="erreur">Erreur lors de la connexion</p> 
                <?php $_SESSION["erreur"]=null; }?>
			</article>
		</main>
	</body>
	
</html>