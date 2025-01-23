<?php
// Démarre la session
session_start();
include('config.php');

// Vérification si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Hacher le mot de passe avant de le stocker
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Préparer la requête pour vérifier si le pseudo ou l'email existe déjà
    $query = "SELECT * FROM users WHERE username = :username OR email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['username' => $username, 'email' => $email]);

    if ($stmt->rowCount() > 0) {
        // Si l'utilisateur ou l'email existe déjà
        echo "<p style='color: red;'>Ce nom d'utilisateur ou cet email est déjà utilisé.</p>";
    } else {
        // Insérer les données dans la base de données
        $query = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'username' => $username,
            'password' => $hashed_password,
            'email' => $email
        ]);

        // Rediriger vers la page de connexion après l'inscription
        header("Location: login.php"); // Redirige vers la page de connexion
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S'inscrire</title>
    <link rel="stylesheet" href="form-styles.css"> <!-- Ajoutez ici votre CSS -->
</head>

<body>
    <div class="form-container">
        <h1>Créer un compte</h1>

        <!-- Formulaire d'inscription -->
        <form action="register.php" method="POST">
            <div class="input-group">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" name="username" id="username" required>
            </div>

            <div class="input-group">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="input-group">
                <label for="password">Mot de passe :</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit">S'inscrire</button>
        </form>

        <p>Vous avez déjà un compte ? <a href="login.php">Connectez-vous ici</a></p>
    </div>
</body>

</html>