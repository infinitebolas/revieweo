<?php
require_once("db.php");

$id = intval($_POST['id_critique'] ?? 0);

$stmt = $db_connection->prepare("UPDATE critique SET epingle = 0 WHERE id_critique = :id");
$success = $stmt->execute(['id' => $id]);

echo json_encode(['success' => $success]);