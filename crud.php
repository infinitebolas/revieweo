<?php

class Critiques {
    private $db;

    public function __construct($db_connection){
        $this->db = $db_connection;
    }

    public function Afficher(int $user_id){
        $stmt = $this->db->prepare("
            SELECT categorie.nom AS categorie, id_critique, titre, contenu, date_creation, note 
            FROM critique
            INNER JOIN user ON critique.id_user = user.id 
            INNER JOIN categorie ON critique.id_categorie = categorie.id
            WHERE user.id = :user
        ");
        $stmt->execute([":user" => $user_id]);
        return $stmt;
    }

    public function AfficherLikes(int $user_id){
        $stmt = $this->db->prepare("
            SELECT *
            FROM critique
            INNER JOIN `like` 
            ON critique.id_critique = `like`.id_critique 
            WHERE `like`.id_user = :user
        ");
        $stmt->execute([":user" => $user_id]);
        return $stmt;
    }

    public function Supprimer(int $critique_id){
        $stmt = $this->db->prepare("
            DELETE FROM critique 
            WHERE id_critique = :id AND id_user = :user
        ");
        $stmt->execute([
            ":id" => $critique_id,
            ":user" => $_SESSION['id_user']
        ]);
        header("Refresh:0");
    }

        public function Dislike(int $critique_id){
        $stmt = $this->db->prepare("
            DELETE FROM `like`
            WHERE id_critique = :id AND id_user = :user
        ");
        $stmt->execute([
            ":id" => $critique_id,
            ":user" => $_SESSION['id_user']
        ]);
        header("Refresh:0");
    }
}

if (isset($_POST['supprimer'])) {
    require_once('db.php');
    $critiques = new Critiques($db_connection); 
    $critiques->Supprimer($_POST['id_critique']); 
}

if (isset($_POST['dislike'])) { 
    require_once('db.php');
    $critiques = new Critiques($db_connection); 
    $critiques->Dislike($_POST['id_critique']); 
}

if (isset($_POST['modifier'])) { 
    header('Location:modify.php');
}