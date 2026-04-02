<?php
class Database {
    private $host = "localhost";
    private $dbname = "revieweo";
    private $user = "root";
    private $pass = "";

    public function connect() {
        return new PDO(
            "mysql:host=$this->host;dbname=$this->dbname;charset=utf8",
            $this->user,
            $this->pass
        );
    }
}