<?php
class Critique {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getCritiqueById($id) {
        $query = "
            SELECT c.*, u.pseudo
            FROM critique c
            JOIN user u ON c.id_user = u.id
            WHERE c.id = :id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCategories($id) {
        $query = "
            SELECT cat.nom
            FROM categorie cat
            JOIN critique_categorie cc ON cat.id = cc.id_categorie
            WHERE cc.id_critique = :id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function countLikes($id) {
        $query = "SELECT COUNT(*) as total FROM `like` WHERE id_critique = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function isLikedByUser($user_id, $critique_id) {
        $query = "SELECT * FROM `like` WHERE id_user = :user AND id_critique = :critique";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ":user" => $user_id,
            ":critique" => $critique_id
        ]);

        return $stmt->rowCount() > 0;
    }
}
?>
