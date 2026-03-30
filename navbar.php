<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Revieweo</title>
</head>
<body>


<div class="login">
<?php
		if (!isset($_SESSION["user_id"])) {
			?>
			<a class="topnav_link" href="register.php">ENREGISTREMENT</a>
			<a class="topnav_link" href="login.php">CONNEXION</a>
			<?php
		} else {
            if ($role)
			?>
			<a class="topnav_link" href="disconnect.php">DECONNEXION</a>
			<?php
		}
	?>
</div>
    
</body>
</html>


