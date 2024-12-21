<?php
$message = "";
$message_class = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $message = addUser($username, $email, $password, $role);
    $message_class = strpos($message, 'succès') !== false ? 'success' : 'error';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Utilisateur</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php if ($message): ?>
        <p class="<?php echo $message_class; ?>"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" class="form-container">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required>
        <label for="role">Rôle:</label>
        <select id="role" name="role">
            <?php
            $roles = getRoles();
            foreach ($roles as $role) {
                echo "<option value='{$role['role_name']}'>{$role['role_name']}</option>";
            }
            ?>
        </select>
        <button type="submit">Ajouter</button>
    </form>
</body>
</html>