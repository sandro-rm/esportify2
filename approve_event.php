// approve_event.php
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

require_once('config.php');

// Vérifier si un événement a été soumis
if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // Mettre à jour l'événement pour qu'il soit approuvé
    $query = "UPDATE events SET status = 'approved' WHERE id = :event_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "L'événement a été approuvé avec succès.";
        header("Location: admin_events.php"); // Rediriger après l'approbation
    } else {
        echo "Une erreur est survenue lors de l'approbation de l'événement.";
    }
} else {
    echo "Événement non trouvé.";
}
?>