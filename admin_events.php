// admin_events.php
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

require_once('config.php');

// Récupérer les événements en attente
$query = "SELECT * FROM events WHERE status = 'pending'";
$stmt = $pdo->query($query);
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements en attente</title>
</head>

<body>
    <h1>Événements en attente de validation</h1>
    <table>
        <tr>
            <th>Titre</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($events as $event): ?>
            <tr>
                <td><?php echo htmlspecialchars($event['title']); ?></td>
                <td><?php echo htmlspecialchars($event['description']); ?></td>
                <td>
                    <form action="approve_event.php" method="POST">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <button type="submit">Approuver</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>