<?php
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
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] != 'secretaire') {
    header('Location: login.php');
    exit();
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['validate_appointment'])) {
        $id = $_POST['id'];
        $message = validateAppointment($id);
    }
}

function getPendingAppointments() {
    global $conn;
    $sql = "SELECT appointments.*, users.username AS doctor_name FROM appointments JOIN users ON appointments.doctor_id = users.id WHERE status='prévu'";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function validateAppointment($id) {
    global $conn;
    $sql = "UPDATE appointments SET status='validé' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return "Rendez-vous validé avec succès!";
    } else {
        return "Erreur: " . $conn->error;
    }
}

$appointments = getPendingAppointments();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation des Rendez-vous</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Validation des Rendez-vous</h1>
        <nav>
            <ul>
                <li><a href="dashboard_secretaire.php">Tableau de Bord</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Bienvenue, <?php echo $_SESSION['user']['username']; ?></h2>
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <h3>Rendez-vous en attente de validation</h3>
        <table class="table-container">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date de naissance</th>
                <th>Maladie</th>
                <th>Médecin</th>
                <th>Date et heure</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
            <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?php echo $appointment['id']; ?></td>
                    <td><?php echo $appointment['nom']; ?></td>
                    <td><?php echo $appointment['prenom']; ?></td>
                    <td><?php echo $appointment['date_naissance']; ?></td>
                    <td><?php echo $appointment['maladie']; ?></td>
                    <td><?php echo $appointment['doctor_name']; ?></td>
                    <td><?php echo $appointment['date']; ?></td>
                    <td><?php echo $appointment['telephone']; ?></td>
                    <td><?php echo $appointment['email']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $appointment['id']; ?>">
                            <button type="submit" name="validate_appointment" class="btn-green">Valider</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>