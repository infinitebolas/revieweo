<?php
class Critique {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $sql = "SELECT * FROM critique ORDER BY epingle DESC, id DESC";
        return $this->db->query($sql)->fetchAll();
    }
}