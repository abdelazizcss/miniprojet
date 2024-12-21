<?php
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role_name = $_POST['role_name'];
    $email = $_POST['email'];
    $message = addRole($role_name, $email);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Rôle</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" class="form-container">
        <label for="role_name">Nom du rôle:</label>
        <input type="text" id="role_name" name="role_name" required>
        <button type="submit">Ajouter</button>
    </form>
</body>
</html>