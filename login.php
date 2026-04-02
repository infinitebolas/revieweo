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
					<input type="email" placeholder="Email" name="email"><br>
					<input type="password" placeholder="Mot de passe" name="mdp"><br>
					<input class="submit-button" type="submit">
				</form>
                <?php if (isset($_SESSION["erreur"])){ ?>
                    <p class="erreur">Erreur lors de la connexion</p> 
					<p><?php echo $_SESSION["erreur"]; ?></p>
                <?php $_SESSION["erreur"]=null; }?>
			</article>
		</main>
	</body>
	
</html>