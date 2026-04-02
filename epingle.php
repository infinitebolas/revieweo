<?php
require_once("db.php");

class Critique {
    private $db;
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function epinglerCritique($id): bool {
        $req = $this->db->prepare("UPDATE critique SET epingle = 1 WHERE id = :id");
        return $req->execute(['id' => $id]);
    }
}

$critique = new Critique($db_connection);
$success = $critique->epinglerCritique($_POST['id_critique'] ?? 0);

echo json_encode(["success" => $success]);