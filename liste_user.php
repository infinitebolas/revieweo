<?php
require_once("db.php");

header('Content-Type: application/json');

$list = $db_connection->prepare("SELECT pseudo FROM user");
$list->execute();

$pseudos = $list->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($pseudos);