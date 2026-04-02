<?php
require_once("db.php");


if (!isset($_POST['id_critique'])) {
    echo json_encode(["success" => false, "error" => "Aucun ID fourni"]);
    exit;
}

$id = intval($_POST['id_critique']);


$stmt = $db_connection->prepare("SELECT epingle FROM critique WHERE id = :id");
$stmt->execute(['id' => $id]);
$critique = $stmt->fetch(PDO::FETCH_ASSOC);

if ($critique) {
    echo json_encode([
        "success" => true,
        "epingle" => (bool)$critique['epingle']
    ]);
} else {
    echo json_encode(["success" => false, "error" => "Critique introuvable"]);
}