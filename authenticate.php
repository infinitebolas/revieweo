<?php
session_start();
$_SESSION["erreur"] = null;

try {
    if (!empty($_POST["mdp"]) && !empty($_POST["email"])) {

        require_once("db.php");

        $db_request = $db_connection->prepare(
            "SELECT id, pseudo, email, password, role FROM user WHERE email = :email"
        );

        $db_request->execute([
            ":email" => $_POST["email"],
        ]);

        $user = $db_request->fetch();

        // Vérifie si l'utilisateur existe ET si le mot de passe est correct
        if ($user && password_verify($_POST["mdp"], $user["password"])) {

            $_SESSION["id_user"] = $user["id"];
            $_SESSION["pseudo"] = $user["pseudo"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            header("Location: index.php");
            exit();

        } else {
            $_SESSION["erreur"] = true;
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION["erreur"] = true;
        header("Location: login.php");
        exit();
    }

} catch (Exception $e) {
    $_SESSION["erreur"] = $e->getMessage();
    exit();
}
?>