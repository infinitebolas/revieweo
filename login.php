<?php
declare(strict_types=1);

session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: critique.php");
    exit;
}

final class Database
{
    private PDO $pdo;

    public function __construct(
        string $host = "127.0.0.1",
        string $dbName = "revieweo",
        string $user = "root",
        string $password = ""
    ) {
        $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
        $this->pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}

final class UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, pseudo, email, password, role
             FROM `user`
             WHERE email = :email
             LIMIT 1"
        );
        $stmt->execute(["email" => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}

$errors = [];

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim((string) ($_POST["email"] ?? ""));
    $password = (string) ($_POST["password"] ?? "");
    $csrfToken = (string) ($_POST["csrf_token"] ?? "");

    if (!hash_equals($_SESSION["csrf_token"], $csrfToken)) {
        $errors[] = "Session invalide, recharge la page puis reessaie.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }

    if ($password === "") {
        $errors[] = "Mot de passe requis.";
    }

    if (empty($errors)) {
        try {
            $database = new Database();
            $userRepository = new UserRepository($database->getConnection());
            $user = $userRepository->findByEmail($email);

            if ($user === null || !password_verify($password, (string) $user["password"])) {
                $errors[] = "Email ou mot de passe incorrect.";
            } else {
                $_SESSION["user_id"] = (int) $user["id"];
                $_SESSION["pseudo"] = (string) $user["pseudo"];
                $_SESSION["email"] = (string) $user["email"];
                $_SESSION["role"] = (string) $user["role"];

                header("Location: critique.php");
                exit;
            }
        } catch (Throwable $throwable) {
            $errors[] = "Erreur lors de la connexion: " . $throwable->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Revieweo - Connexion</title>
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
      <h1>Connexion</h1>
      <p>Connecte-toi pour acceder aux critiques protegees.</p>

      <?php if (!empty($errors)): ?>
        <div class="auth-error" role="alert">
          <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form class="auth-form" action="login.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8") ?>" />

        <label for="email">Email</label>
        <input id="email" name="email" type="email" placeholder="exemple@mail.com" required />

        <label for="password">Mot de passe</label>
        <input id="password" name="password" type="password" placeholder="********" required />

        <button class="btn primary auth-submit" type="submit">Se connecter</button>
      </form>

      <p class="auth-link">
        Pas encore de compte ? <a href="register.php">Inscris-toi</a>
      </p>
    </section>
  </main>
</body>
</html>
