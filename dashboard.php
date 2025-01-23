<?php
// Inclure le fichier de configuration pour la connexion à la base de données
include('config.php');


// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // Rediriger si l'utilisateur n'est pas connecté
    exit();
}

// Récupérer les événements créés par l'utilisateur et validés
$stmt_events_user = $pdo->prepare("SELECT * FROM events WHERE created_by = ? AND status = 'validé' ORDER BY start_date DESC");
$stmt_events_user->execute([$_SESSION['user_id']]);
$events_user = $stmt_events_user->fetchAll();

// Récupérer tous les événements validés (ceux visibles pour tous les utilisateurs)
$stmt_events_all = $pdo->prepare("SELECT * FROM events WHERE status = 'validé' ORDER BY start_date DESC");
$stmt_events_all->execute();
$events_all = $stmt_events_all->fetchAll();

// Ajouter un événement aux favoris
if (isset($_GET['add_favorite'])) {
    $event_id = $_GET['add_favorite'];
    $user_id = $_SESSION['user_id'];

    // Vérifier si l'événement est déjà dans les favoris
    $stmt_check_favorite = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND event_id = ?");
    $stmt_check_favorite->execute([$user_id, $event_id]);
    if ($stmt_check_favorite->rowCount() == 0) {
        // Ajouter l'événement aux favoris
        $stmt_add_favorite = $pdo->prepare("INSERT INTO favorites (user_id, event_id) VALUES (?, ?)");
        $stmt_add_favorite->execute([$user_id, $event_id]);
    }
}

// Récupérer les événements favoris de l'utilisateur
$stmt_favorites = $pdo->prepare("SELECT events.* FROM events 
                                JOIN favorites ON events.id = favorites.event_id 
                                WHERE favorites.user_id = ? 
                                ORDER BY events.start_date DESC");
$stmt_favorites->execute([$_SESSION['user_id']]);
$favorites = $stmt_favorites->fetchAll();


$stmt_scores = $pdo->prepare("
    SELECT s.score, e.title AS event_title, e.start_date, e.end_date, s.username
    FROM scores s
    JOIN events e ON s.event_id = e.id
    WHERE s.username = ? AND e.end_date < NOW()
    ORDER BY e.end_date DESC
");
$stmt_scores->execute([$_SESSION['username']]);
$scores = $stmt_scores->fetchAll();


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Tableau de Bord</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #1a1a1a;
            color: #ccc;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            background-color: #1E90FF;
            color: white;
            padding: 20px;
            margin: 0;
            font-size: 32px;
        }

        .container {
            width: 85%;
            margin: 20px auto;
        }

        .button {
            padding: 12px 24px;
            background-color: #1E90FF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            margin-bottom: 30px;
            font-size: 16px;
            display: inline-block;
        }

        .button:hover {
            background-color: #4682B4;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .dashboard-header a {
            padding: 10px 20px;
            background-color: #1E90FF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .dashboard-header a:hover {
            background-color: #4682B4;
        }

        .event-card {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            padding: 20px;
            transition: transform 0.2s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .event-card h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: white;
        }

        .event-card p {
            font-size: 16px;
            color: #ccc;
            margin: 5px 0;
        }

        .event-status {
            font-weight: bold;
            color: #28a745;
        }

        .event-status.en-attente {
            color: #ffc107;
        }

        .event-status.rejete {
            color: #dc3545;
        }

        .event-card-footer {
            margin-top: 15px;
            font-size: 14px;
            color: #888;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #1E90FF;
            color: white;
        }

        footer a {
            color: white;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
            }

            .event-card {
                padding: 15px;
            }

            h1 {
                font-size: 26px;
            }
        }
    </style>
</head>

<body>


    <h1>Mon Tableau de Bord</h1>

    <div class="container">
        <div class="dashboard-header">
            <!-- Lien vers la page d'accueil -->
            <a href="index.html" class="button">Retour à l'Accueil</a>

            <!-- Bouton pour créer un nouvel événement -->
            <a href="create_event.php" class="button">Créer un Nouvel Événement</a>

            <!-- Lien de déconnexion -->
            <a href="logout.php" class="button" style="background-color: #dc3545;">Se Déconnecter</a>
        </div>

        <h2>Mes Événements</h2>

        <?php if ($events_user): ?>
            <?php foreach ($events_user as $event): ?>
                <div class="event-card">
                    <h2><?= htmlspecialchars($event['title']); ?></h2>
                    <p><strong>Description :</strong> <?= htmlspecialchars($event['description']); ?></p>
                    <p><strong>Date de début :</strong> <?= htmlspecialchars($event['start_date']); ?></p>
                    <p><strong>Date de fin :</strong> <?= htmlspecialchars($event['end_date']); ?></p>
                    <p><strong>Nombre de joueurs :</strong> <?= htmlspecialchars($event['player_count']); ?></p>
                    <p><strong>Status :</strong>
                        <span class="event-status <?= strtolower($event['status']); ?>">
                            <?= htmlspecialchars($event['status']); ?>
                        </span>
                    </p>
                    <div class="event-card-footer">
                        <p><strong>Créé par :</strong> <?= htmlspecialchars($event['created_by']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun événement validé trouvé.</p>
        <?php endif; ?>

        <h2>Tous les Événements Validés</h2>

        <?php if ($events_all): ?>
            <?php foreach ($events_all as $event): ?>
                <div class="event-card">
                    <h2><?= htmlspecialchars($event['title']); ?></h2>
                    <p><strong>Description :</strong> <?= htmlspecialchars($event['description']); ?></p>
                    <p><strong>Date de début :</strong> <?= htmlspecialchars($event['start_date']); ?></p>
                    <p><strong>Date de fin :</strong> <?= htmlspecialchars($event['end_date']); ?></p>
                    <p><strong>Nombre de joueurs :</strong> <?= htmlspecialchars($event['player_count']); ?></p>
                    <p><strong>Status :</strong>
                        <span class="event-status <?= strtolower($event['status']); ?>">
                            <?= htmlspecialchars($event['status']); ?>
                        </span>
                    </p>
                    <div class="event-card-footer">
                        <p><strong>Créé par :</strong> <?= htmlspecialchars($event['created_by']); ?></p>
                    </div>
                    <a href="join_event.php?event_id=<?= $event['id'] ?>" class="button">Rejoindre l'événement</a>
                    <a href="chat_event.php?event_id=<?= $event['id'] ?>" class="button">Rejoindre le chat</a>
                    <a href="dashboard.php?add_favorite=<?= $event['id']; ?>" class="button">Mettre en favoris</a>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun événement validé pour le moment.</p>
        <?php endif; ?>
    </div>
    <h2>Événements Favoris</h2>

    <?php if ($favorites): ?>
        <?php foreach ($favorites as $event): ?>
            <div class="event-card">
                <h2><?= htmlspecialchars($event['title']); ?></h2>
                <p><strong>Description :</strong> <?= htmlspecialchars($event['description']); ?></p>
                <p><strong>Date de début :</strong> <?= htmlspecialchars($event['start_date']); ?></p>
                <p><strong>Date de fin :</strong> <?= htmlspecialchars($event['end_date']); ?></p>
                <p><strong>Nombre de joueurs :</strong> <?= htmlspecialchars($event['player_count']); ?></p>
                <p><strong>Status :</strong>
                    <span class="event-status <?= strtolower($event['status']); ?>">
                        <?= htmlspecialchars($event['status']); ?>
                    </span>
                </p>
                <div class="event-card-footer">

                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun événement favori pour le moment.</p>
    <?php endif; ?>


    <h2>Historique des Scores</h2>

    <?php if ($scores): ?>
        <?php foreach ($scores as $score): ?>
            <div class="score-card">
                <h3><?= htmlspecialchars($score['event_title']); ?></h3>
                <p><strong>Date de l'événement :</strong> <?= htmlspecialchars($score['start_date']); ?> - <?= htmlspecialchars($score['end_date']); ?></p>
                <p><strong>Score :</strong> <?= htmlspecialchars($score['score']); ?></p>
                <p><strong>Joueur :</strong> <?= htmlspecialchars($score['username']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun score enregistré.</p>
    <?php endif; ?>

    <footer>
        <p>&copy; 2024 Mon Site E-sport. <a href="contact.php">Contact</a></p>
    </footer>

</body>

</html>