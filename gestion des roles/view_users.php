<?php
$users = getUsers();
$roles = getRoles();
$message = "";
$message_class = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $email = $_POST['delete_email'];
    $message = deleteUser($email);
    $message_class = strpos($message, 'succès') !== false ? 'success' : 'error';
    $users = getUsers(); // Refresh the user list after deletion
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir Utilisateurs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php if ($message): ?>
        <p class="<?php echo $message_class; ?>"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php
    foreach ($roles as $role) {
        echo "<h2>Rôle: {$role['role_name']}</h2>";
        echo "<table class='table-container'>";
        echo "<tr><th>Nom d'utilisateur</th><th>Email</th><th>Rôle</th><th>Action</th></tr>";
        foreach ($users as $user) {
            if ($user['role_id'] == $role['id']) {
                echo "<tr><td>{$user['username']}</td><td>{$user['email']}</td><td>{$role['role_name']}</td>";
                echo "<td><form method='POST' style='display:inline;'><input type='hidden' name='delete_email' value='{$user['email']}'><button type='submit' name='delete_user'>Supprimer</button></form></td></tr>";
            }
        }
        echo "</table>";
    }
    ?>
</body>
</html>