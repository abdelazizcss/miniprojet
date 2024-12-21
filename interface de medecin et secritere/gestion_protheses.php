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

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_prosthesis'])) {
        $patient_id = $_POST['patient_id'];
        $prosthetist_name = $_POST['prosthetist_name'];
        $type = $_POST['type'];
        $status = $_POST['status'];
        $payment_status = $_POST['payment_status'];
        $message = addProsthesis($patient_id, $prosthetist_name, $type, $status, $payment_status);
    } elseif (isset($_POST['delete_prosthesis'])) {
        $id = $_POST['id'];
        $message = deleteProsthesis($id);
    }
}

function getProstheses($filter = 'all') {
    global $conn;
    $status_filter = "";
    if ($filter != 'all') {
        $status_filter = "WHERE status='$filter'";
    }
    $sql = "SELECT prostheses.*, patients.username AS patient_name 
            FROM prostheses 
            JOIN users AS patients ON prostheses.patient_id = patients.id 
            $status_filter";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addProsthesis($patient_id, $prosthetist_name, $type, $status, $payment_status) {
    global $conn;
    $sql = "INSERT INTO prostheses (patient_id, prosthetist_name, type, status, payment_status) 
            VALUES ($patient_id, '$prosthetist_name', '$type', '$status', '$payment_status')";
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

function getAcceptedPatients() {
    global $conn;
    $sql = "SELECT id, username FROM users WHERE role_id = (SELECT id FROM roles WHERE role_name = 'patient')";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$prostheses = getProstheses($filter);
$patients = getAcceptedPatients();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Prothèses</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Gestion des Prothèses</h1>
        <nav>
            <ul>
                <li><a href="dashboard_medecin.php">Tableau de Bord</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Bienvenue, <?php echo $_SESSION['user']['username']; ?></h2>
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <h3>Ajouter une prothèse</h3>
        <form method="POST" class="form-container">
            <label for="patient_id">Patient:</label>
            <select id="patient_id" name="patient_id" required>
                <?php foreach ($patients as $patient): ?>
                    <option value="<?php echo $patient['id']; ?>"><?php echo $patient['username']; ?></option>
                <?php endforeach; ?>
            </select>
            <label for="prosthetist_name">Nom du prothésiste:</label>
            <input type="text" id="prosthetist_name" name="prosthetist_name" required>
            <label for="type">Type de prothèse:</label>
            <select id="type" name="type" required>
                <option value="amovible">Amovible</option>
                <option value="fixe">Fixe</option>
                <option value="implant">Implant</option>
                <option value="esthetique">Esthétique</option>
                <option value="orthodontique">Orthodontique</option>
            </select>
            <label for="status">État:</label>
            <select id="status" name="status" required>
                <option value="en cours">En cours</option>
                <option value="terminé">Terminé</option>
                <option value="annulé">Annulé</option>
            </select>
            <label for="payment_status">État du paiement:</label>
            <select id="payment_status" name="payment_status" required>
                <option value="payé">Payé</option>
                <option value="non payé">Non payé</option>
            </select>
            <button type="submit" name="add_prosthesis">Ajouter</button>
        </form>
        <h3>Filtrer les prothèses</h3>
        <form method="GET" class="form-container">
            <label for="filter">Filtrer par état:</label>
            <select id="filter" name="filter" onchange="this.form.submit()">
                <option value="all" <?php if ($filter == 'all') echo 'selected'; ?>>Tous</option>
                <option value="en cours" <?php if ($filter == 'en cours') echo 'selected'; ?>>En cours</option>
                <option value="terminé" <?php if ($filter == 'terminé') echo 'selected'; ?>>Terminé</option>
                <option value="annulé" <?php if ($filter == 'annulé') echo 'selected'; ?>>Annulé</option>
            </select>
        </form>
        <h3>Prothèses</h3>
        <table class="table-container">
            <tr>
                <th>ID de la prothèse</th>
                <th>Nom du patient</th>
                <th>Nom du prothésiste</th>
                <th>Type</th>
                <th>État</th>
                <th>État du paiement</th>
                <th>Action</th>
            </tr>
            <?php foreach ($prostheses as $prosthesis): ?>
                <tr>
                    <td><?php echo $prosthesis['id']; ?></td>
                    <td><?php echo $prosthesis['patient_name']; ?></td>
                    <td><?php echo $prosthesis['prosthetist_name']; ?></td>
                    <td><?php echo $prosthesis['type']; ?></td>
                    <td><?php echo $prosthesis['status']; ?></td>
                    <td><?php echo $prosthesis['payment_status']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $prosthesis['id']; ?>">
                            <button type="submit" name="delete_prosthesis">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>