<?php
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM user WHERE email=?";
        $req = $this->db->prepare($sql);
        $req->execute([$email]);

        $user = $req->fetch();

        if($user && $user['password'] == $password) {
            return $user;
        }

        return false;
    }

    public function register($pseudo, $email, $password) {
        $sql = "INSERT INTO user(pseudo,email,password,role) VALUES(?,?,?,?)";
        $req = $this->db->prepare($sql);
        return $req->execute([$pseudo, $email, $password, 'utilisateur']);
    }
}