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
        <?php     
            if (isset($_SESSION["id_user"]) and ($_SESSION["role"]=="critique" or $_SESSION["role"]=="administrateur")){
                require_once ('crud.php'); 
                require_once('db.php');                  
                $critiques = new Critiques($db_connection);
                ?>
                <h2>Bonjour <?=$_SESSION["pseudo"]?></h2>
                <p class="titre"> Critiques personnelles : </p>
                <div class="dashboard">                 
                <?php
                    $critiquesListes = $critiques->Afficher($_SESSION["id_user"])->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($critiquesListes as $critique){ ?>
                        <article class="articles">
                            <p> Titre de la critique: <b><?=$critique['titre']?></b></p><br>
                            <p> Catégorie: <b><?=$critique['categorie']?></b></p><br>
                            <p> Publiée le: <b><?=$critique['date_creation']?></b></p><br>
                            <p>Note: <b><?=$critique['note']?>/10</b></p><br>
                            <p><?=$critique['contenu']?></p><br>
                            <form method="post">
                                <input type="hidden" name="id_critique" value="<?=$critique['id_critique']?>">
                                <button type="submit" name="supprimer">Supprimer</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="id_critique" value="<?=$critique['id_critique']?>">
                                <button type="submit" name="modifier">Modifier</button>
                            </form>
                        </article>
                    <?php } ?>
                </div> 
                <p class="titre"> Critiques likées: </p>
                <div class="dashboard">                 
                <?php
                    $critiquesListes = $critiques->AfficherLikes($_SESSION["id_user"])->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($critiquesListes as $critique){ ?>
                        <article class="articles">
                            <p> Titre de la critique: <b><?=$critique['titre']?></b></p><br>
                            <p> Publiée le: <b><?=$critique['date_creation']?></b></p><br>
                            <p>Note: <?=$critique['note']?>/10</p><br>
                            <p><?=$critique['contenu']?></p><br>
                            <form method="post">
                                <input type="hidden" name="id_critique" value="<?=$critique['id_critique']?>">
                                <button type="submit" name="dislike">Retirer le like</button>
                            </form>
                        </article>
                    <?php } ?>
                </div> 
            <?php } else { ?>
                <p>Access denied</p>
            <?php } ?>
    </main>    
</body>
</html>