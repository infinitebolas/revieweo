<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login1.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$userId = (int) $_SESSION["user_id"];

final class CategorieRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT id, nom FROM categorie ORDER BY nom ASC"
        );
        return $stmt->fetchAll();
    }

    public function existsById(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM categorie WHERE id = :id LIMIT 1");
        $stmt->execute(["id" => $id]);
        return (bool) $stmt->fetch();
    }
}

final class CritiqueRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findOwnedWithCategory(int $critiqueId, int $ownerId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.id, c.titre, c.contenu, c.note, c.id_user, cc.id_categorie
             FROM critique c
             LEFT JOIN critique_categorie cc ON cc.id_critique = c.id
             WHERE c.id = :id AND c.id_user = :id_user
             LIMIT 1"
        );
        $stmt->execute([
            "id" => $critiqueId,
            "id_user" => $ownerId,
        ]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateOwned(
        int $critiqueId,
        int $ownerId,
        string $titre,
        string $contenu,
        float $note,
        int $idCategorie
    ): void {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE critique
                 SET titre = :titre, contenu = :contenu, note = :note
                 WHERE id = :id AND id_user = :id_user"
            );
            $stmt->execute([
                "titre" => $titre,
                "contenu" => $contenu,
                "note" => $note,
                "id" => $critiqueId,
                "id_user" => $ownerId,
            ]);
            if ($stmt->rowCount() === 0) {
                throw new RuntimeException("Critique introuvable ou acces refuse.");
            }

            $del = $this->pdo->prepare(
                "DELETE FROM critique_categorie WHERE id_critique = :id_critique"
            );
            $del->execute(["id_critique" => $critiqueId]);

            $ins = $this->pdo->prepare(
                "INSERT INTO critique_categorie (id_critique, id_categorie)
                 VALUES (:id_critique, :id_categorie)"
            );
            $ins->execute([
                "id_critique" => $critiqueId,
                "id_categorie" => $idCategorie,
            ]);

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}

$errors = [];
$successMessage = "";
$categories = [];
$critiqueRow = null;
$critiqueId = (int) ($_GET["id"] ?? 0);

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    $categories = (new CategorieRepository($pdo))->findAll();

    if ($critiqueId > 0) {
        $critiqueRow = (new CritiqueRepository($pdo))->findOwnedWithCategory($critiqueId, $userId);
    }
} catch (Throwable $throwable) {
    $errors[] = "Erreur de chargement: " . $throwable->getMessage();
}

if ($critiqueId <= 0 || $critiqueRow === null) {
    header("Location: critique1.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = trim((string) ($_POST["titre"] ?? ""));
    $contenu = trim((string) ($_POST["contenu"] ?? ""));
    $noteRaw = trim((string) ($_POST["note"] ?? ""));
    $idCategorie = (int) ($_POST["id_categorie"] ?? 0);
    $csrfToken = (string) ($_POST["csrf_token"] ?? "");
    $postCritiqueId = (int) ($_POST["critique_id"] ?? 0);

    if ($postCritiqueId !== $critiqueId) {
        $errors[] = "Identifiant de critique invalide.";
    }

    if (!hash_equals($_SESSION["csrf_token"], $csrfToken)) {
        $errors[] = "Session invalide, recharge la page puis reessaie.";
    }

    if ($titre === "" || mb_strlen($titre) < 3) {
        $errors[] = "Le titre doit contenir au moins 3 caracteres.";
    }

    if ($contenu === "" || mb_strlen($contenu) < 20) {
        $errors[] = "Le contenu doit contenir au moins 20 caracteres.";
    }

    if (!is_numeric($noteRaw)) {
        $errors[] = "La note doit etre un nombre.";
    }

    $note = (float) $noteRaw;
    if ($note < 0 || $note > 10) {
        $errors[] = "La note doit etre comprise entre 0 et 10.";
    }

    if ($idCategorie <= 0) {
        $errors[] = "Choisis une categorie.";
    } else {
        $databaseCheck = new Database();
        if (!(new CategorieRepository($databaseCheck->getConnection()))->existsById($idCategorie)) {
            $errors[] = "Categorie invalide.";
        }
    }

    if (empty($errors)) {
        try {
            $database = new Database();
            (new CritiqueRepository($database->getConnection()))->updateOwned(
                $critiqueId,
                $userId,
                $titre,
                $contenu,
                $note,
                $idCategorie
            );
            $successMessage = "Critique mise a jour avec succes.";
            $critiqueRow = (new CritiqueRepository($database->getConnection()))->findOwnedWithCategory(
                $critiqueId,
                $userId
            );
        } catch (Throwable $throwable) {
            $errors[] = "Erreur lors de la mise a jour: " . $throwable->getMessage();
        }
    }

    if (!empty($errors) && $critiqueRow !== null) {
        $critiqueRow["titre"] = $titre;
        $critiqueRow["contenu"] = $contenu;
        $critiqueRow["note"] = $note;
        $critiqueRow["id_categorie"] = $idCategorie;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Revieweo - Modifier une critique</title>
  <link rel="stylesheet" href="style1css" />
</head>
<body class="page-login">
  <header>
    <div class="container nav">
      <a href="index.html" class="brand">Review<span>eo</span></a>
      <nav>
        <ul>
          <li><a href="index.html">Accueil</a></li>
          <li><a href="critique1.php">Critiques</a></li>
        </ul>
      </nav>
      <div class="nav-actions">
        <span class="btn"><?= htmlspecialchars((string) $_SESSION["pseudo"], ENT_QUOTES, "UTF-8") ?></span>
        <a class="btn primary" href="logout1.php">Se deconnecter</a>
      </div>
    </div>
  </header>

  <main class="container auth-layout">
    <section class="auth-card hero-card">
      <h1>Modifier la critique</h1>
      <p>Tu peux modifier uniquement les critiques que tu as creees.</p>

      <?php if (!empty($errors)): ?>
        <div class="auth-error" role="alert">
          <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($successMessage !== ""): ?>
        <div class="auth-success" role="status">
          <p><?= htmlspecialchars($successMessage, ENT_QUOTES, "UTF-8") ?></p>
        </div>
      <?php endif; ?>

      <form class="auth-form" action="editcritique.php?id=<?= (int) $critiqueId ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8") ?>" />
        <input type="hidden" name="critique_id" value="<?= (int) $critiqueId ?>" />

        <label for="titre">Titre</label>
        <input id="titre" name="titre" type="text" value="<?= htmlspecialchars((string) ($critiqueRow["titre"] ?? ""), ENT_QUOTES, "UTF-8") ?>" required />

        <label for="contenu">Contenu</label>
        <textarea id="contenu" name="contenu" rows="7" required><?= htmlspecialchars((string) ($critiqueRow["contenu"] ?? ""), ENT_QUOTES, "UTF-8") ?></textarea>

        <label for="note">Note (/10)</label>
        <input id="note" name="note" type="number" min="0" max="10" step="0.1" value="<?= htmlspecialchars((string) ($critiqueRow["note"] ?? ""), ENT_QUOTES, "UTF-8") ?>" required />

        <label for="id_categorie">Categorie</label>
        <?php if (empty($categories)): ?>
          <p class="auth-error" role="alert">Aucune categorie en base.</p>
        <?php else: ?>
          <select id="id_categorie" name="id_categorie" required>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= (int) $cat['id'] ?>" <?= (int) ($critiqueRow["id_categorie"] ?? 0) === (int) $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars((string) $cat['nom'], ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        <?php endif; ?>

        <button class="btn primary auth-submit" type="submit" <?= empty($categories) ? 'disabled' : '' ?>>Enregistrer</button>
        <p class="auth-link"><a href="critique1.php">Retour aux critiques</a></p>
      </form>
    </section>
  </main>
</body>
</html>
