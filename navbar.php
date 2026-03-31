
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Revieweo</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>


<div class="login">
	<a class="topnav_link" href="">Accueil</a>
	<a class="topnav_link" href="">Critiques</a>
<?php
		if (!isset($_SESSION["pseudo"])) {
			?>			
			<a class="topnav_link" href="register.php">Enregistrement</a>
			<a class="topnav_link" href="login.php">Connexion</a>
		<?php }
		else {			
			if ($_SESSION["role"]=="critique" or $_SESSION["role"]=="administrateur"){
				?>
				<a class="topnav_link" href="">Création</a>
				<a class="topnav_link" href="">Dashboard</a>
			<?php
			}
			if ($_SESSION["role"]=="administrateur"){
				?>
				<a class="topnav_link" href="">Gérer</a>
			<?php } ?>
			<a class="topnav_link" href="disconnect.php">Deconnexion</a>
			<?php } ?>
</div>
    
</body>
</html>


