<?php
require 'config.php'; // Connexion à la base

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $role = $_POST['role'];

    // Vérifier les données
    if (in_array($role, ['user', 'employee', 'admin'])) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $id]);
        header('Location: admin_dashboard.php?success=role_updated');
        exit;
    } else {
        header('Location: admin_dashboard.php?error=invalid_role');
        exit;
    }
}
