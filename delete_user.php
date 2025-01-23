<?php
require 'config.php'; // Connexion à la base

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Supprimer l'utilisateur
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: admin_dashboard.php?success=user_deleted');
    exit;
}
