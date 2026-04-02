<?php
require_once("db.php");

header('Content-Type: application/json');

class User {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllPseudos(): array {
        $pseudo = $this->db->prepare("SELECT pseudo, role FROM user");
        $pseudo->execute();
        return $pseudo->fetchAll(PDO::FETCH_ASSOC);
    }
}


$user = new User($db_connection);
$results = $user->getAllPseudos();


echo json_encode($results);