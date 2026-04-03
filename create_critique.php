<?php
require_once("db.php"); 
session_start();

try {
    // Vérifier que les champs sont envoyés
    if (!isset($_POST['titre'], $_POST['contenu'], $_POST['note'], $_POST['categorie_id'])) {
        echo json_encode(['error' => 'Tous les champs sont requis']);
        exit;
    }

    // Récupérer et sécuriser les données
    $titre = htmlspecialchars(trim($_POST['titre']));
    $contenu = htmlspecialchars(trim($_POST['contenu']));
    $note = intval($_POST['note']);
    $categorie_id = intval($_POST['categorie_id']);

    // Validation simple
    if ($note < 0 || $note > 10) {
        echo json_encode(['error' => 'La note doit être entre 0 et 10']);
        exit;
    }

    if ($categorie_id <= 0) {
        echo json_encode(['error' => 'Catégorie invalide']);
        exit;
    }

    // Requête préparée pour l'insertion
    $stmt = $db_connection->prepare("
        INSERT INTO critique (titre, contenu, note, id_categorie, date_creation, id_user)
        VALUES (:titre, :contenu, :note, :categorie_id, NOW(), :id) 
    ");

    $stmt->bindParam(':titre', $titre);
    $stmt->bindParam(':contenu', $contenu);
    $stmt->bindParam(':note', $note, PDO::PARAM_INT);
    $stmt->bindParam(':categorie_id', $categorie_id, PDO::PARAM_INT);
    $stmt->bindParam(':id', $_SESSION["id_user"]);

    if ($stmt->execute()) {
        header("Location: index.php");
    }

} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Erreur serveur',
        'message' => $e->getMessage()
    ]);
}