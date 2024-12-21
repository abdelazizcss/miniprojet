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

// Gestion des utilisateurs et des rôles
function getUsers() {
    global $conn;
    $sql = "SELECT users.id, users.username, users.email, users.role_id, roles.role_name FROM users JOIN roles ON users.role_id = roles.id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addUser($username, $email, $password, $role) {
    global $conn;
    // Vérifier si l'email existe déjà
    $checkEmailSql = "SELECT * FROM users WHERE email='$email'";
    $checkEmailResult = $conn->query($checkEmailSql);
    if ($checkEmailResult->num_rows > 0) {
        return "Cet email est déjà utilisé.";
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password, role_id) VALUES ('$username', '$email', '$hashed_password', (SELECT id FROM roles WHERE role_name='$role'))";
    if ($conn->query($sql) === TRUE) {
        return "Utilisateur ajouté avec succès!";
    } else {
        return "Erreur: " . $conn->error;
    }
}

function deleteUser($email) {
    global $conn;
    $sql = "DELETE FROM users WHERE email='$email'";
    if ($conn->query($sql) === TRUE) {
        return "Utilisateur supprimé avec succès!";
    } else {
        return "Erreur: " . $conn->error;
    }
}

function addRole($role_name, $email) {
    global $conn;
    // Vérifier si l'email existe déjà
    $checkEmailSql = "SELECT * FROM users WHERE email='$email'";
    $checkEmailResult = $conn->query($checkEmailSql);
    if ($checkEmailResult->num_rows > 0) {
        return "Cet email est déjà utilisé.";
    }
    
    $sql = "INSERT INTO roles (role_name) VALUES ('$role_name')";
    if ($conn->query($sql) === TRUE) {
        return "Rôle ajouté avec succès!";
    } else {
        return "Erreur: " . $conn->error;
    }
}

function getRoles() {
    global $conn;
    $sql = "SELECT * FROM roles";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
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

// Gestion des prothèses
function getProstheses($filter = 'all') {
    global $conn;
    $status_filter = "";
    if ($filter != 'all') {
        $status_filter = "WHERE status='$filter'";
    }
    $sql = "SELECT prostheses.*, patients.username AS patient_name, prosthetists.username AS prosthetist_name 
            FROM prostheses 
            JOIN users AS patients ON prostheses.patient_id = patients.id 
            JOIN users AS prosthetists ON prostheses.prosthetist_id = prosthetists.id 
            $status_filter";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addProsthesis($patient_id, $prosthetist_id, $type, $status, $payment_status) {
    global $conn;
    $sql = "INSERT INTO prostheses (patient_id, prosthetist_id, type, status, payment_status) 
            VALUES ($patient_id, $prosthetist_id, '$type', '$status', '$payment_status')";
    if ($conn->query($sql) === TRUE) {
        return "Prothèse ajoutée avec succès!";
    } else {
        return "Erreur: " . $conn->error;
    }
}

function deleteProsthesis($id) {
    global $conn;
    $sql = "DELETE FROM prostheses WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return "Prothèse supprimée avec succès!";
    } else {
        return "Erreur: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Rôles</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Gestion des Rôles</h1>
        <nav>
            <ul>
                <li><a href="?page=add_user">Ajouter Utilisateur</a></li>
                <li><a href="?page=add_role">Ajouter Rôle</a></li>
                <li><a href="?page=view_users">Voir Utilisateurs</a></li>
                <li><a href="?page=view_roles">Voir Rôles</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <?php
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            switch ($page) {
                case 'add_user':
                    include 'add_user.php';
                    break;
                case 'add_role':
                    include 'add_role.php';
                    break;
                case 'view_users':
                    include 'view_users.php';
                    break;
                case 'view_roles':
                    include 'view_roles.php';
                    break;
                default:
                    echo "<h2>Bienvenue sur la page de gestion des rôles</h2>";
                    echo "<p>Utilisez le menu pour naviguer entre les différentes sections.</p>";
                    break;
            }
        } else {
            echo "<h2>Bienvenue sur la page de gestion des rôles</h2>";
            echo "<p>Utilisez le menu pour naviguer entre les différentes sections.</p>";
        }
        ?>
    </main>
    <script src="scripts.js"></script>
</body>
</html>