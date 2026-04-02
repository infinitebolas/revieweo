<?php
require_once("db.php");


class Critique {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllCritiques(): array {
        $crit = $this->db->prepare("
            SELECT c.id, c.titre, c.contenu, u.pseudo
            FROM critique c
            LEFT JOIN user u ON c.id_user = u.id
            ORDER BY c.id
        ");
        $crit->execute();
        return $crit->fetchAll(PDO::FETCH_ASSOC);
    }
}


$critique = new Critique($db_connection);
$results = $critique->getAllCritiques();


echo json_encode($results);