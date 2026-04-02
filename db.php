<?php
class Database {
    private $host = "localhost";
    private $dbname = "revieweo";
    private $user = "root";
    private $pass = "";

    public function connect() {
        try {
            return new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8",
                $this->user,
                $this->pass
            );
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}