<?php

// Démarrer la session
session_start();

// Inclure le fichier de configuration pour la base de données
require_once('config.php');

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    // Rediriger vers le tableau de bord en fonction du rôle
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérifier les informations de connexion dans la base de données
    $query = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $query->execute(['username' => $username]);
    $user = $query->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Si le mot de passe est correct, démarrer la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Stocker le rôle de l'utilisateur (admin ou user)

        // Rediriger vers le tableau de bord en fonction du rôle
        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php"); // Tableau de bord admin
        } else {
            header("Location: dashboard.php"); // Tableau de bord utilisateur classique
        }
        exit();
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter</title>
    <link rel="stylesheet" href="form-styles.css"> <!-- Inclure ton fichier CSS -->
</head>

<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Se connecter</h2>

            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="textbox">
                    <input type="text" name="username" placeholder="Nom d'utilisateur" required>
                </div>
                <div class="textbox">
                    <input type="password" name="password" placeholder="Mot de passe" required>
                </div>
                <input type="submit" value="Se connecter" class="btn">
            </form>

            <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
        </div>
    </div>
</body>

</html>