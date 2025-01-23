<?php
// events.php

session_start();
require_once('config.php');

// Initialisation des filtres
$filter_player_count = isset($_GET['player_count']) ? $_GET['player_count'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';
$filter_username = isset($_GET['username']) ? $_GET['username'] : '';

try {
    // Construction de la requête avec filtres
    $sql = "SELECT events.*, users.username
            FROM events
            JOIN users ON events.created_by = users.id
            WHERE events.status = 'validé'";

    // Ajout des conditions de filtrage
    if ($filter_player_count) {
        $sql .= " AND events.player_count >= :player_count";
    }
    if ($filter_date) {
        $sql .= " AND DATE(events.start_date) = :date";
    }
    if ($filter_username) {
        $sql .= " AND users.username LIKE :username";
    }

    $sql .= " ORDER BY start_date ASC";

    $stmt = $pdo->prepare($sql);

    // Binding des paramètres pour la requête préparée
    if ($filter_player_count) {
        $stmt->bindParam(':player_count', $filter_player_count, PDO::PARAM_INT);
    }
    if ($filter_date) {
        $stmt->bindParam(':date', $filter_date, PDO::PARAM_STR);
    }
    if ($filter_username) {
        $stmt->bindParam(':username', $filter_username, PDO::PARAM_STR);
    }

    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si la requête est une requête AJAX, retourner les données en JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($events);
        exit; // Arrêter l'exécution ici pour les requêtes AJAX
    }
} catch (PDOException $e) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    } else {
        die("Erreur lors de la récupération des événements : " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements E-sport</title>
    <style>
        /* Style général de la page */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #0f0f0f;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: rgba(105, 105, 105, 0.3);
            padding: 15px 30px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav a {
            color: #00acee;
            text-decoration: none;
            font-size: 18px;
            padding: 10px 20px;
            border-radius: 4px;
            transition: background 0.3s, color 0.3s;
        }

        nav a:hover {
            background: #00acee;
            color: #fff;
        }

        .event-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
            margin-top: 20px;
        }

        .event-card {
            background-color: rgba(105, 105, 105, 0.3);
            border-radius: 12px;
            width: 300px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 15px rgb(2, 2, 2);
        }

        .event-title {
            font-size: 1.8em;
            color: #00acee;
            margin-bottom: 15px;
            text-align: center;
        }

        .event-description {
            font-size: 1em;
            color: #ccc;
            margin-bottom: 15px;
            text-align: center;
        }

        .event-meta {
            color: #888;
            font-size: 0.9em;
            text-align: center;
            margin-bottom: 20px;
        }

        .join-button {
            display: block;
            width: 100%;
            text-align: center;
            background-color: #00acee;
            color: #fff;
            padding: 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .join-button:hover {
            background-color: #007bb5;
        }

        footer {
            background-color: #1a1a2e;
            text-align: center;
            padding: 10px;
            color: #888;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <a href="index.html">Accueil</a>
            <a href="login.php">Se connecter / Tableau de Bord</a>
        </nav>
    </header>

    <main>
        <h1 style="text-align: center; color: #fff; font-size: 2.5em;">Événements E-sport à venir</h1>

        <!-- Formulaire de filtre -->
        <form id="filter-form" style="text-align: center; margin-bottom: 20px;">
            <input type="number" id="filter-player-count" placeholder="Nombre de joueurs min" style="padding: 8px; margin: 5px;">
            <input type="date" id="filter-date" style="padding: 8px; margin: 5px;">
            <input type="text" id="filter-username" placeholder="Pseudo créateur" style="padding: 8px; margin: 5px;">
            <button type="button" id="apply-filter" style="background-color: #00acee; color: #fff; padding: 10px 20px; border-radius: 4px;">Filtrer</button>
        </form>

        <div class="event-list" id="event-list">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p class="event-description"><?php echo htmlspecialchars($event['description']); ?></p>
                        <div class="event-meta">
                            <p><strong>Créé par :</strong> <?php echo htmlspecialchars($event['username']); ?></p>
                            <p><strong>Participants :</strong> <?php echo htmlspecialchars($event['player_count']); ?></p>
                            <p><strong>Date :</strong> <?php echo htmlspecialchars($event['start_date']); ?> - <?php echo htmlspecialchars($event['end_date']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #ccc;">Aucun événement à afficher pour le moment.</p>
            <?php endif; ?>
        </div>
    </main>

    <script>
        document.getElementById('apply-filter').addEventListener('click', () => {
            const playerCount = document.getElementById('filter-player-count').value;
            const date = document.getElementById('filter-date').value;
            const username = document.getElementById('filter-username').value;

            const params = new URLSearchParams({
                player_count: playerCount,
                date: date,
                username: username
            });

            fetch('events.php?' + params.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const eventList = document.getElementById('event-list');
                    eventList.innerHTML = '';

                    if (data.length > 0) {
                        data.forEach(event => {
                            const card = document.createElement('div');
                            card.className = 'event-card';
                            card.innerHTML = `
                                <h3 class="event-title">${event.title}</h3>
                                <p class="event-description">${event.description}</p>
                                <div class="event-meta">
                                    <p><strong>Créé par :</strong> ${event.username}</p>
                                    <p><strong>Participants :</strong> ${event.player_count}</p>
                                    <p><strong>Date :</strong> ${event.start_date} - ${event.end_date}</p>
                                </div>
                            `;
                            eventList.appendChild(card);
                        });
                    } else {
                        eventList.innerHTML = '<p style="text-align: center; color: #ccc;">Aucun événement à afficher pour le moment.</p>';
                    }
                })
                .catch(error => console.error('Erreur :', error));
        });
    </script>
</body>

</html>