<?php
// db.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dental";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}
session_start();
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $user = authenticateUser($username, $email, $password, $role);
    if ($user) {
        $_SESSION['user'] = $user;
        if ($user['role_name'] == 'medecin') {
            header('Location: dashboard_medecin.php');
        } elseif ($user['role_name'] == 'secretaire') {
            header('Location: dashboard_secretaire.php');
        }
        exit();
    } else {
        $message = "Nom, email, mot de passe ou rôle incorrect.";
    }
}

function authenticateUser($username, $email, $password, $role) {
    global $conn;
    $sql = "SELECT users.*, roles.role_name FROM users JOIN roles ON users.role_id = roles.id WHERE username='$username' AND email='$email' AND roles.role_name='$role'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            return $user;
        }
    }
    return false;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php if ($message): ?>
        <p class="error"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" class="form-container">
        <label for="username">Nom:</label>
        <input type="text" id="username" name="username" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required>
        <label for="role">Rôle:</label>
        <select id="role" name="role" required>
            <option value="medecin">Médecin</option>
            <option value="secretaire">Secrétaire</option>
        </select>
        <button type="submit">Connexion</button>
    </form>
</body>
</html>