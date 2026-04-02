<?php
require_once('db.php');
class Critique {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAll(): array {
        $sql = "SELECT * FROM critique ORDER BY epingle DESC, id_critique DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}