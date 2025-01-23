<?php
require 'config.php'; // Assurez-vous que votre config.php est bien inclus

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Vérifiez si l'ID de l'événement est valide
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :event_id AND status = 'validé'");
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        die("ID d'événement non valide ou événement non validé.");
    }

    // Récupération de l'ID de l'utilisateur (assurez-vous que l'utilisateur est connecté)
    session_start();  // Si vous utilisez des sessions
    if (!isset($_SESSION['user_id'])) {
        die("Vous devez être connecté pour rejoindre un événement.");
    }

    $user_id = $_SESSION['user_id'];

    // Vérifiez que l'utilisateur n'a pas déjà rejoint cet événement
    $stmt = $pdo->prepare("SELECT * FROM event_users WHERE event_id = :event_id AND user_id = :user_id");
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        die("Vous avez déjà rejoint cet événement.");
    }

    // Ajoutez l'utilisateur à l'événement
    $stmt = $pdo->prepare("INSERT INTO event_users (event_id, user_id) VALUES (:event_id, :user_id)");
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Vous avez rejoint l'événement avec succès.";
        header("Location: dashboard.php");  // Redirige vers le tableau de bord après l'inscription
        exit();
    } else {
        echo "Une erreur est survenue lors de l'inscription à l'événement.";
    }
} else {
    echo "Aucun événement sélectionné.";
}
