<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require_once __DIR__ . "/config/database.php";

final class LikeRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function exists(int $userId, int $critiqueId): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1
             FROM `like`
             WHERE id_user = :id_user AND id_critique = :id_critique
             LIMIT 1"
        );
        $stmt->execute([
            "id_user" => $userId,
            "id_critique" => $critiqueId,
        ]);
        return (bool) $stmt->fetchColumn();
    }

    public function add(int $userId, int $critiqueId): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `like` (id_user, id_critique)
             VALUES (:id_user, :id_critique)"
        );
        $stmt->execute([
            "id_user" => $userId,
            "id_critique" => $critiqueId,
        ]);
    }

    public function remove(int $userId, int $critiqueId): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM `like`
             WHERE id_user = :id_user AND id_critique = :id_critique"
        );
        $stmt->execute([
            "id_user" => $userId,
            "id_critique" => $critiqueId,
        ]);
    }

    public function getLikedCritiqueIds(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_critique
             FROM `like`
             WHERE id_user = :id_user"
        );
        $stmt->execute(["id_user" => $userId]);

        $likedIds = [];
        foreach ($stmt->fetchAll() as $row) {
            $likedIds[] = (int) $row["id_critique"];
        }

        return $likedIds;
    }
}

final class CritiqueRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function existsById(int $critiqueId): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM critique WHERE id = :id LIMIT 1");
        $stmt->execute(["id" => $critiqueId]);
        return (bool) $stmt->fetch();
    }

    public function isOwnedByUser(int $critiqueId, int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT id
             FROM critique
             WHERE id = :id AND id_user = :id_user
             LIMIT 1"
        );
        $stmt->execute([
            "id" => $critiqueId,
            "id_user" => $userId,
        ]);
        return (bool) $stmt->fetch();
    }

    public function deleteOwnedCritique(int $critiqueId, int $userId): bool
    {
        if (!$this->isOwnedByUser($critiqueId, $userId)) {
            return false;
        }

        $this->pdo->beginTransaction();
        try {
            $deleteLikesStmt = $this->pdo->prepare("DELETE FROM `like` WHERE id_critique = :id_critique");
            $deleteLikesStmt->execute(["id_critique" => $critiqueId]);

            $deleteCritiqueStmt = $this->pdo->prepare(
                "DELETE FROM critique
                 WHERE id = :id AND id_user = :id_user"
            );
            $deleteCritiqueStmt->execute([
                "id" => $critiqueId,
                "id_user" => $userId,
            ]);

            $this->pdo->commit();
            return $deleteCritiqueStmt->rowCount() > 0;
        } catch (Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    public function findAllWithAuthor(): array
    {
        $stmt = $this->pdo->query(
            "SELECT c.id, c.titre, c.contenu, c.note, c.date_creation, c.id_user, u.pseudo
             FROM critique c
             LEFT JOIN `user` u ON u.id = c.id_user
             ORDER BY c.date_creation DESC, c.id DESC"
        );
        return $stmt->fetchAll();
    }
}

$errors = [];
$userId = (int) $_SESSION["user_id"];
$isAjaxRequest = isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower((string) $_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest";

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    $likeRepository = new LikeRepository($pdo);
    $critiqueRepository = new CritiqueRepository($pdo);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $csrfToken = (string) ($_POST["csrf_token"] ?? "");
        $critiqueId = (int) ($_POST["critique_id"] ?? 0);
        $action = (string) ($_POST["action"] ?? "like");
        $likedNow = false;

        if (!hash_equals($_SESSION["csrf_token"], $csrfToken)) {
            $errors[] = "Session invalide, recharge la page puis reessaie.";
        } elseif (!$critiqueRepository->existsById($critiqueId)) {
            $errors[] = "Critique invalide.";
        } elseif ($action === "delete") {
            $deleted = $critiqueRepository->deleteOwnedCritique($critiqueId, $userId);
            if (!$deleted) {
                $errors[] = "Tu ne peux supprimer que tes propres critiques.";
            }
        } else {
            if ($likeRepository->exists($userId, $critiqueId)) {
                $likeRepository->remove($userId, $critiqueId);
                $likedNow = false;
            } else {
                $likeRepository->add($userId, $critiqueId);
                $likedNow = true;
            }
        }

        if ($isAjaxRequest) {
            header("Content-Type: application/json; charset=utf-8");

            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode([
                    "ok" => false,
                    "message" => $errors[0],
                ]);
            } else {
                echo json_encode([
                    "ok" => true,
                    "liked" => $likedNow,
                ]);
            }
            exit;
        }
    }

    $critiques = $critiqueRepository->findAllWithAuthor();
    $likedCritiqueIds = $likeRepository->getLikedCritiqueIds($userId);
} catch (Throwable $throwable) {
    $errors[] = "Erreur de gestion des likes: " . $throwable->getMessage();
    $critiques = [];
    $likedCritiqueIds = [];

    if ($isAjaxRequest) {
        header("Content-Type: application/json; charset=utf-8");
        http_response_code(500);
        echo json_encode([
            "ok" => false,
            "message" => "Erreur serveur.",
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Revieweo - Critiques de Films</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body class="page-critiques">
  <header>
    <div class="container nav">
      <a href="index.html" class="brand">Review<span>eo</span></a>
      <nav>
        <ul>
          <li><a href="index.html">Accueil</a></li>
          <li><a href="critique.php">Critiques</a></li>
        </ul>
      </nav>
      <div class="nav-actions">
        <?php if (($_SESSION["role"] ?? "") === "critique"): ?>
          <a class="btn" href="addcritique.php">Ajouter une critique</a>
        <?php endif; ?>
        <span class="btn"><?= htmlspecialchars((string) $_SESSION["pseudo"], ENT_QUOTES, "UTF-8") ?></span>
        <a class="btn primary" href="logout.php">Se deconnecter</a>
      </div>
    </div>
  </header>

  <main class="container">
    <?php if (!empty($errors)): ?>
      <div class="auth-error" role="alert">
        <?php foreach ($errors as $error): ?>
          <p><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <section class="hero">
      <div class="grid">
        <?php foreach ($critiques as $critique): ?>
          <article class="movie-card">
            <div class="movie-cover"></div>
            <div class="movie-content">
              <div class="movie-title">
                <h3><?= htmlspecialchars((string) $critique["titre"], ENT_QUOTES, "UTF-8") ?></h3>
                <span class="score"><?= number_format((float) $critique["note"], 1) ?>/10</span>
              </div>
              <p class="meta">
                Par <?= htmlspecialchars((string) ($critique["pseudo"] ?? "Utilisateur"), ENT_QUOTES, "UTF-8") ?>
                - <?= htmlspecialchars((string) $critique["date_creation"], ENT_QUOTES, "UTF-8") ?>
              </p>
              <p class="excerpt"><?= nl2br(htmlspecialchars((string) $critique["contenu"], ENT_QUOTES, "UTF-8")) ?></p>
              <div class="card-actions">
                <form method="post" class="like-form">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8") ?>" />
                  <input type="hidden" name="action" value="like" />
                  <input type="hidden" name="critique_id" value="<?= (int) $critique["id"] ?>" />
                  <button class="like-btn <?= in_array((int) $critique["id"], $likedCritiqueIds, true) ? "is-liked" : "" ?>" type="submit">
                    <?= in_array((int) $critique["id"], $likedCritiqueIds, true) ? "Liked" : "Like" ?>
                  </button>
                </form>

                <?php if ((int) $critique["id_user"] === $userId): ?>
                  <form method="post" onsubmit="return confirm('Supprimer cette critique ?');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8") ?>" />
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="critique_id" value="<?= (int) $critique["id"] ?>" />
                    <button class="btn delete-btn" type="submit">Supprimer</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endforeach; ?>

        <?php if (empty($critiques)): ?>
          <article class="movie-card">
            <div class="movie-content">
              <div class="movie-title">
                <h3>Aucune critique</h3>
                <span class="score">--</span>
              </div>
              <p class="excerpt">Aucune critique n'est encore disponible dans la base de donnees.</p>
            </div>
          </article>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <footer>
    <div class="container"></div>
  </footer>
  <script>
    const likeForms = document.querySelectorAll(".like-form");

    likeForms.forEach((form) => {
      form.addEventListener("submit", async (event) => {
        event.preventDefault();

        const button = form.querySelector(".like-btn");
        if (!button) return;

        button.disabled = true;

        try {
          const response = await fetch("critique.php", {
            method: "POST",
            headers: {
              "X-Requested-With": "XMLHttpRequest",
              "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
            },
            body: new URLSearchParams(new FormData(form)).toString(),
          });

          const data = await response.json();
          if (!response.ok || !data.ok) {
            throw new Error(data.message || "Erreur lors du like.");
          }

          if (data.liked) {
            button.classList.add("is-liked");
            button.textContent = "Liked";
          } else {
            button.classList.remove("is-liked");
            button.textContent = "Like";
          }
        } catch (error) {
          alert(error.message || "Erreur lors du like.");
        } finally {
          button.disabled = false;
        }
      });
    });
  </script>
</body>
</html>
