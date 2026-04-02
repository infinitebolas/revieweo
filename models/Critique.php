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
            LEFT JOIN user u ON c.id_user = u.id
            WHERE c.id_critique = :id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([":id" => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getCategories($id) {
        $query = "
            SELECT cat.nom
            FROM categorie cat
            JOIN critique c ON c.id_categorie = cat.id
            WHERE c.id_critique = :id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([":id" => $id]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public function countLikes($id) {
        $query = "SELECT COUNT(*) as total FROM `like` WHERE id_critique = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([":id" => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    public function isLikedByUser($user_id, $critique_id) {
        $query = "
            SELECT 1 FROM `like`
            WHERE id_user = :user AND id_critique = :critique
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ":user" => $user_id,
            ":critique" => $critique_id
        ]);

        return $stmt->rowCount() > 0;
    }
}
?>