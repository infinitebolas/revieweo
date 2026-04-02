<?php
session_start();
require_once "config/Database.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$critique_id = $_POST['id'];

$database = new Database();
$db = $database->getConnection();

// check si déjà liké
$query = "SELECT * FROM `like` WHERE id_user = :user AND id_critique = :critique";
$stmt = $db->prepare($query);
$stmt->execute([
    ":user" => $user_id,
    ":critique" => $critique_id
]);

if ($stmt->rowCount() > 0) {
    // UNLIKE
    $query = "DELETE FROM `like` WHERE id_user = :user AND id_critique = :critique";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ":user" => $user_id,
        ":critique" => $critique_id
    ]);
    $action = "unliked";
} else {
    // LIKE
    $query = "INSERT INTO `like` (id_user, id_critique) VALUES (:user, :critique)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ":user" => $user_id,
        ":critique" => $critique_id
    ]);
    $action = "liked";
}

// nouveau total
$query = "SELECT COUNT(*) as total FROM `like` WHERE id_critique = :id";
$stmt = $db->prepare($query);
$stmt->execute([":id" => $critique_id]);
$total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

echo json_encode([
    "success" => true,
    "likes" => $total,
    "action" => $action
]);
