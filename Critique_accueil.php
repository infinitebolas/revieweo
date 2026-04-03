<?php
require_once('db.php');
class Critique {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

public function getAll(): array {
    $sql = "SELECT 
                critique.id_critique,
                critique.titre,
                critique.contenu,
                critique.note,
                critique.epingle,
                categorie.nom AS categorie
            FROM critique
            INNER JOIN categorie ON critique.id_categorie = categorie.id
            ORDER BY critique.epingle DESC, critique.id_critique DESC";

    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}