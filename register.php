<?php
declare(strict_types=1);

session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: critique.php");
    exit;
}
require_once __DIR__ . "/config/database.php";

final class User
{
    public function __construct(
        public string $pseudo,
        public string $email,
        public string $passwordHash,
        public string $role
    ) {
    }
}

final class UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM `user` WHERE email = :email LIMIT 1");
        $stmt->execute(["email" => $email]);
        return (bool) $stmt->fetch();
    }

    public function create(User $user): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `user` (pseudo, email, password, role)
             VALUES (:pseudo, :email, :password, :role)"
        );
        $stmt->execute([
            "pseudo" => $user->pseudo,
            "email" => $user->email,
            "password" => $user->passwordHash,
            "role" => $user->role,
        ]);
    }
}

$errors = [];
$successMessage = "";

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pseudo = trim((string) ($_POST["pseudo"] ?? ""));
    $email = trim((string) ($_POST["email"] ?? ""));
    $password = (string) ($_POST["password"] ?? "");
    $role = (string) ($_POST["role"] ?? "utilisateur");
    $csrfToken = (string) ($_POST["csrf_token"] ?? "");

    if (!hash_equals($_SESSION["csrf_token"], $csrfToken)) {
        $errors[] = "Session invalide, recharge la page puis reessaie.";
    }

    if ($pseudo === "" || mb_strlen($pseudo) < 2) {
        $errors[] = "Le pseudo doit contenir au moins 2 caracteres.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caracteres.";
    }

    $allowedRoles = ["utilisateur", "critique", "administrateur"];
    if (!in_array($role, $allowedRoles, true)) {
        $errors[] = "Role invalide.";
    }

    if (empty($errors)) {
        try {
            $database = new Database();
            $userRepository = new UserRepository($database->getConnection());

            if ($userRepository->emailExists($email)) {
                $errors[] = "Cet email est deja utilise.";
            } else {
                $user = new User(
                    pseudo: $pseudo,
                    email: $email,
                    passwordHash: password_hash($password, PASSWORD_DEFAULT),
                    role: $role
                );
                $userRepository->create($user);
                $successMessage = "Compte cree avec succes. Tu peux maintenant te connecter.";
            }
        } catch (Throwable $throwable) {
            $errors[] = "Erreur lors de l'inscription: " . $throwable->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Revieweo - Inscription</title>
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
        <a class="btn" href="login.php">Se connecter</a>
        <a class="btn primary" href="register.php">Inscription</a>
      </div>
    </div>
  </header>

  <main class="container auth-layout">
    <section class="auth-card hero-card">
      <h1>Inscription</h1>
      <p>Inscris-toi pour publier des critiques et liker tes films preferes.</p>

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

      <form class="auth-form" action="register.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8") ?>" />

        <label for="pseudo">Pseudo</label>
        <input id="pseudo" name="pseudo" type="text" placeholder="Pseudo" required />

        <label for="email">Email</label>
        <input id="email" name="email" type="email" placeholder="exemple@mail.com" required />

        <label for="password">Mot de passe</label>
        <input id="password" name="password" type="password" placeholder="********" required />

        <label for="role">Role</label>
        <select id="role" name="role" required>
          <option value="utilisateur">Utilisateur</option>
          <option value="critique">Critique</option>
          <option value="administrateur">Administrateur</option>
        </select>

        <button class="btn primary auth-submit" type="submit">Inscription</button>
      </form>
    </section>
  </main>
</body>
</html>
