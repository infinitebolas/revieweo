<?php
class Critique {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $sql = "SELECT critique.*, user.pseudo 
                FROM critique 
                JOIN user ON critique.id_user = user.id 
                ORDER BY date_creation DESC";

        return $this->db->query($sql)->fetchAll();
    }
}