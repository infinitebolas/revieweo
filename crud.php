<?php
    
class Critiques{
    
    public function Afficher(int $user_id){
        require_once('db.php');
        $db_critiques = $db_connection->prepare("SELECT id_critique, titre, contenu, date_creation, note FROM critique
            INNER JOIN user ON critique.id_user = user.id WHERE user.id = :user"); 
        $db_critiques -> execute([":user" => $user_id]);
        return $db_critiques;
    }

    public function Supprimer(int $critique_id):void{
        require_once('db.php');
        $db_critiques = $db_connection->prepare("DELETE FROM critique WHERE id_critique = :id AND id_user = :user"); 
        $db_critiques -> execute(
            [":id" => $critique_id,
            ":user" => $_SESSION['id_user']
        ]);
    }
}

if (isset($_POST['supprimer'])) {
    $critiques = new Critiques();
    $critiques->Supprimer($_POST['id_critique']);
}