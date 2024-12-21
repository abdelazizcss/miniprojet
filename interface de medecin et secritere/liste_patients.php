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
$message_type = "";
$search = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['search_patient'])) {
        $search = $_POST['search'];
        $patients = searchPatients($search);
    } elseif (isset($_POST['mark_present'])) {
        $id = $_POST['id'];
        $message = markPresent($id);
        $message_type = "success";
        $patients = getValidatedPatients();
    }
} else {
    $patients = getValidatedPatients();
}

function getValidatedPatients() {
    global $conn;
    $sql = "SELECT * FROM appointments WHERE status='validé'";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function searchPatients($search) {
    global $conn;
    $sql = "SELECT * FROM appointments WHERE nom LIKE '%$search%' AND status='validé'";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function markPresent($id) {
    global $conn;
    $sql = "UPDATE appointments SET presence=TRUE WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return "Patient marqué comme présent!";
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
    <title>Gestion des Patients</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Gestion des Patients</h1>
        <nav>
            <ul>
                <li><a href="dashboard_secretaire.php">Tableau de Bord</a></li>
                <li><a href="validation_rendezvous.php">Validation des Rendez-vous</a></li>
                <li><a href="salle_attente.php">Salle d'attente</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Bienvenue, <?php echo $_SESSION['user']['username']; ?></h2>
        <?php if ($message): ?>
            <p class="message <?php echo $message_type; ?>"><?php echo $message; ?></p>
        <?php endif; ?>
        <h3>Rechercher un patient</h3>
        <form method="POST" class="form-container">
            <label for="search">Nom du patient:</label>
            <input type="text" id="search" name="search" value="<?php echo $search; ?>" required>
            <button type="submit" name="search_patient">Rechercher</button>
        </form>
        <h3>Liste des patients validés</h3>
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
            <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?php echo $patient['id']; ?></td>
                    <td><?php echo $patient['nom']; ?></td>
                    <td><?php echo $patient['prenom']; ?></td>
                    <td><?php echo $patient['date_naissance']; ?></td>
                    <td><?php echo $patient['maladie']; ?></td>
                    <td><?php echo $patient['doctor_id']; ?></td>
                    <td><?php echo $patient['date']; ?></td>
                    <td><?php echo $patient['telephone']; ?></td>
                    <td><?php echo $patient['email']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $patient['id']; ?>">
                            <button type="submit" name="mark_present" class="btn-green">Marquer comme présent</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>