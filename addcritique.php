<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (($_SESSION["role"] ?? "") !== "critique") {
    http_response_code(403);
}
require_once __DIR__ . "/config/database.php";

final class CritiqueRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(string $titre, string $contenu, float $note, int $idUser): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO critique (titre, contenu, note, date_creation, id_user)
             VALUES (:titre, :contenu, :note, NOW(), :id_user)"
        );
        $stmt->execute([
            "titre" => $titre,
            "contenu" => $contenu,
            "note" => $note,
            "id_user" => $idUser,
        ]);
    }
}

$errors = [];
$successMessage = "";

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if (($_SESSION["role"] ?? "") === "critique" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = trim((string) ($_POST["titre"] ?? ""));
    $contenu = trim((string) ($_POST["contenu"] ?? ""));
    $noteRaw = trim((string) ($_POST["note"] ?? ""));
    $csrfToken = (string) ($_POST["csrf_token"] ?? "");

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

    if (empty($errors)) {
        try {
            $database = new Database();
            $repository = new CritiqueRepository($database->getConnection());
            $repository->create($titre, $contenu, $note, (int) $_SESSION["user_id"]);
            $successMessage = "Critique ajoutee avec succes.";
        } catch (Throwable $throwable) {
            $errors[] = "Erreur lors de l'ajout de la critique: " . $throwable->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Revieweo - Ajouter une critique</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body class="page-login">
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
        <span class="btn"><?= htmlspecialchars((string) $_SESSION["pseudo"], ENT_QUOTES, "UTF-8") ?></span>
        <a class="btn primary" href="logout.php">Se deconnecter</a>
      </div>
    </div>
  </header>

  <main class="container auth-layout">
    <section class="auth-card hero-card">
      <h1>Ajouter une critique</h1>

      <?php if (($_SESSION["role"] ?? "") !== "critique"): ?>
        <div class="auth-error" role="alert">
          <p>Acces refuse: seul un utilisateur avec le role "critique" peut publier une critique.</p>
        </div>
      <?php else: ?>
        <p>Partage ton avis sur un film avec la communaute.</p>

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

        <form class="auth-form" action="addcritique.php" method="post">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8") ?>" />

          <label for="titre">Titre</label>
          <input id="titre" name="titre" type="text" placeholder="Titre de la critique" required />

          <label for="contenu">Contenu</label>
          <textarea id="contenu" name="contenu" rows="7" placeholder="Ton avis detaille sur le film..." required></textarea>

          <label for="note">Note (/10)</label>
          <input id="note" name="note" type="number" min="0" max="10" step="0.1" placeholder="Ex: 8.5" required />

          <button class="btn primary auth-submit" type="submit">Publier la critique</button>
        </form>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
