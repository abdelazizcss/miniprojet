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
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] != 'medecin') {
    header('Location: login.php');
    exit();
}

// Récupérer le nombre de patients dans la salle d'attente
$sql = "SELECT COUNT(*) AS count FROM appointments WHERE presence=TRUE";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$patients_in_waiting_room = $row['count'];

// Récupérer le nombre total de rendez-vous
$sql = "SELECT COUNT(*) AS count FROM appointments";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_appointments = $row['count'];

// Récupérer le nombre de rendez-vous validés
$sql = "SELECT COUNT(*) AS count FROM appointments WHERE status='validé'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$validated_appointments = $row['count'];

// Récupérer le nombre de rendez-vous par mois
$current_month = date('Y-m');
$sql = "SELECT COUNT(*) AS count FROM appointments WHERE DATE_FORMAT(date, '%Y-%m') = '$current_month'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$appointments_this_month = $row['count'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Médecin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Tableau de Bord Médecin</h1>
        <nav>
            <ul>
                <li><a href="dashboard_medecin.php">Tableau de Bord</a></li>
                <li><a href="calendrier.php">Calendrier</a></li>
                <li><a href="gestion_stock.php">Gestion de Stock</a></li>
                <li><a href="gestion_protheses.php">Gestion de Protheses</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Bienvenue, <?php echo $_SESSION['user']['username']; ?></h2>
        <section class="dashboard-summary">
            <div class="summary-card">
                <h3>Patients dans la salle d'attente</h3>
                <p><?php echo $patients_in_waiting_room; ?></p>
            </div>
            <div class="summary-card">
                <h3>Total des rendez-vous</h3>
                <p><?php echo $total_appointments; ?></p>
            </div>
            <div class="summary-card">
                <h3>Rendez-vous validés</h3>
                <p><?php echo $validated_appointments; ?></p>
            </div>
            <div class="summary-card">
                <h3>Rendez-vous ce mois-ci</h3>
                <p><?php echo $appointments_this_month; ?></p>
            </div>
        </section>
    </main>
</body>
</html>