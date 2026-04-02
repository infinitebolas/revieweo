<?php
session_start();

require_once "config/Database.php";
require_once "models/Critique.php";

$database = new Database();
$db = $database->getConnection();

$critiqueModel = new Critique($db);

$id = $_GET['id'] ?? null;
if (!$id) die("Erreur");

$critique = $critiqueModel->getCritiqueById($id);
$categories = $critiqueModel->getCategories($id);
$likes = $critiqueModel->countLikes($id);

$userLiked = false;
if (isset($_SESSION['user_id'])) {
    $userLiked = $critiqueModel->isLikedByUser($_SESSION['user_id'], $id);
}

function afficherEtoiles($note)
{
    $etoiles = "";
    for ($i = 1; $i <= 10; $i++) {
        $etoiles .= ($i <= $note) ? "⭐" : "☆";
    }
    return $etoiles;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Critique</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f6fa;
        }

        .card {
            margin-top: 50px;
        }

        .like-btn.liked {
            background-color: gray;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <?php require_once('navbar.php'); ?>
    <div class="container">
        <div class="card shadow p-4">

            <h2><?= htmlspecialchars($critique['titre']) ?></h2>

            <p class="text-muted">
                Ecrit par: <?= htmlspecialchars($critique['pseudo']) ?> |
                <?= $critique['date_creation'] ?>
            </p>

            <p>
                <?php foreach ($categories as $cat): ?>
                    <span class="badge bg-primary"><?= htmlspecialchars($cat) ?></span>
                <?php endforeach; ?>
            </p>

            <hr>

            <p><?= nl2br(htmlspecialchars($critique['contenu'])) ?></p>

            <hr>

            <p>
                <strong>Note :</strong>
                <?= afficherEtoiles($critique['note']) ?>
                (<?= $critique['note'] ?>/10)
            </p>

            <p>
                <strong>Likes :</strong>
                <span id="likeCount"><?= $likes ?></span>
            </p>

            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-secondary">⬅ Retour</a>

                <button
                    id="likeBtn"
                    class="btn <?= $userLiked ? 'btn-secondary' : 'btn-danger' ?>">
                    <?= $userLiked ? '💔 Unlike' : '❤️ Like' ?>
                </button>

            </div>

        </div>
    </div>

    <script>
        const btn = document.getElementById("likeBtn");

        btn.addEventListener("click", function() {

            fetch("like.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "id=<?= $id ?>"
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {

                        // update compteur
                        document.getElementById("likeCount").innerText = data.likes;

                        // toggle bouton
                        if (data.action === "liked") {
                            btn.innerText = "💔 Unlike";
                            btn.classList.remove("btn-danger");
                            btn.classList.add("btn-secondary");
                        } else {
                            btn.innerText = "❤️ Like";
                            btn.classList.remove("btn-secondary");
                            btn.classList.add("btn-danger");
                        }
                    }
                });
        });
    </script>


</body>

</html>