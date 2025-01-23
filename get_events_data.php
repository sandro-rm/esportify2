<?php
// Connexion à la base de données
require_once 'config.php';

// Requête pour obtenir les données des événements
$stmt = $pdo->prepare("SELECT DATE(start_date) AS event_date, COUNT(id) AS total_events FROM events GROUP BY DATE(start_date)");
$stmt->execute();

// Récupérer les résultats sous forme de tableau associatif
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retourner les données au format JSON
echo json_encode($data);
