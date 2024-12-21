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

// Récupérer la liste des patients en salle d'attente
$sql = "SELECT * FROM appointments WHERE presence=TRUE";
$waiting_patients = $conn->query($sql);

// Récupérer les rendez-vous
$sql = "SELECT * FROM appointments";
$appointments = $conn->query($sql);

// Préparer les événements pour FullCalendar
$events = [];
while ($appointment = $appointments->fetch_assoc()) {
    $events[] = [
        'title' => $appointment['nom'] . ' ' . $appointment['prenom'],
        'start' => $appointment['date']
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des Rendez-vous</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: <?php echo json_encode($events); ?>
            });
        });
    </script>
</head>
<body>
    <header>
        <h1>Calendrier des Rendez-vous</h1>
        <nav>
            <ul>
                <li><a href="dashboard_medecin.php">Tableau de Bord</a></li>
                <li><a href="calendrier.php">Calendrier</a></li>
                <li><a href="patients.php">Patients</a></li>
                <li><a href="gestion_stock.php">Gestion de Stock</a></li>
                <li><a href="gestion_prothese.php">Gestion de Prothèse</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="calendar-section">
            <div id="calendar"></div>
        </section>
        <section class="waiting-room">
            <h2>Patients en salle d'attente</h2>
            <ul>
                <?php while ($patient = $waiting_patients->fetch_assoc()): ?>
                    <li><?php echo $patient['nom'] . ' ' . $patient['prenom']; ?></li>
                <?php endwhile; ?>
            </ul>
        </section>
    </main>
</body>
</html>