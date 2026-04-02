<?php
require_once("db.php");

class Critique {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function deleteCritique(int $id): array {
        $crit = "DELETE FROM critique WHERE id = :id";
        $supp = $this->db->prepare($crit);
        $supp->bindParam(':id', $id, PDO::PARAM_INT);

        if ($supp->execute()) {
            return ["success" => true];
        } else {
            return ["success" => false, "error" => "Suppression échoué"];
        }
    }
}


if (isset($_POST['id_critique'])) {
    $id = intval($_POST['id_critique']);


    $critique = new Critique($db_connection);
    $results = $critique->deleteCritique($id);

    echo json_encode($results);
}