<?php
    session_start()
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <?php require_once("navbar.php"); ?>
    <main>
        <div class="dashboard"> 
        <?php
        
            if (isset($_SESSION["id"]) and ($_SESSION["role"]=="critique" or $_SESSION["role"]=="administrateur")){
                require_once('db.php');
                ?>
                <h2>Bonjour <?=$_SESSION["pseudo"]?></h2>
                <?php
                $db_critiques = $db_connection->prepare("SELECT titre, contenu, date_creation FROM critique
                INNER JOIN user ON critique.id_user = user.id WHERE user.id = :user"); 
                $db_critiques -> execute([":user" => $_SESSION["id"]]);
                $critiques = $db_critiques->fetchAll(PDO::FETCH_ASSOC);
                foreach ($critiques as $critique){ ?>
                    <article class="articles">
                        <p> Titre de la critique <b><?=$critique['titre']?></b></p><br>
                        <p> Publiée le: <b><?=$critique['date_creation']?></b></p><br>
                        <p><?=$critique['contenu']?></p><br>
                    </article>
			<?php }} ?>
        <div> 
    </main>    
</body>
</html>