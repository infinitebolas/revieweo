<?php
require_once("db.php");
header('Content-Type: application/json');

class User {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function deleteByPseudo(string $pseudo): bool {
        $pseudos = $this->db->prepare("DELETE FROM user WHERE pseudo = :pseudo");
        return $pseudos->execute(['pseudo' => $pseudo]);
    }
}


$pseudo = $_POST['pseudo'] ?? null;

if (!$pseudo) {
    echo json_encode(["success" => false, "message" => "Pseudo manquant"]);
    exit;
}


$user = new User($db_connection);
$results = $user->deleteByPseudo($pseudo);


echo json_encode(["success" => $results]);