<?php
require_once 'config.php';

// Vérifier que l'ID est bien passé dans l'URL
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Mise à jour du statut de l'événement
    $stmt = $pdo->prepare("UPDATE events SET status = 'validé' WHERE id = :id");
    $stmt->bindParam(':id', $event_id);
    $stmt->execute();

    // Rediriger vers le tableau de bord après avoir accepté l'événement
    header('Location: admin_dashboard.php');
    exit();
} else {
    // Si aucun ID n'est passé, rediriger ou afficher un message d'erreur
    echo "Erreur : l'événement n'a pas pu être trouvé.";
}
