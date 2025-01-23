<?php
// event_detail.php

session_start();
require_once('config.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Vérifier si un ID d'événement est passé dans l'URL
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Récupérer les détails de l'événement
    $query = "SELECT * FROM events WHERE id = :event_id AND status = 'approved'";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();

    $event = $stmt->fetch();

    if (!$event) {
        echo "L'événement n'existe pas ou n'est pas approuvé.";
        exit();
    }
} else {
    echo "Aucun événement sélectionné.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'événement</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="events.php">Événements</a></li>
                <li><a href="dashboard.php">Tableau de bord</a></li>
                <li><a href="logout.php">Se déconnecter</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1><?php echo htmlspecialchars($event['title']); ?></h1>
        <p><strong>Description : </strong><?php echo htmlspecialchars($event['description']); ?></p>
        <p><strong>Participants :</strong> <?php echo htmlspecialchars($event['player_count']); ?></p>
        <p><strong>Date :</strong> <?php echo htmlspecialchars($event['start_date']); ?> - <?php echo htmlspecialchars($event['end_date']); ?></p>

        <a href="join_event.php?id=<?php echo $event['id']; ?>" class="btn">Rejoindre l'événement</a>
    </main>

    <footer>
        <p>&copy; 2024 E-sport Events</p>
    </footer>
</body>

</html>