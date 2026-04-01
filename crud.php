<?php

class Critiques{
    
    public function Afficher(int $id){
        require_once ('db.php');
        $db_critiques = $db_connection->prepare("SELECT titre, contenu, date_creation, note FROM critique
            INNER JOIN user ON critique.id_user = user.id WHERE user.id = :user"); 
        $db_critiques -> execute([":user" => $id]);
        return $db_critiques;
    }
}