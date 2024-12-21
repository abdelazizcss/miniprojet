<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$message = "";
$message_type = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_appointment'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $date_naissance = $_POST['date_naissance'];
        $maladie = $_POST['maladie'];
        $doctor_id = $_POST['doctor_id'];
        $date = $_POST['date'];
        $telephone = $_POST['telephone'];
        $email = $_POST['email'];
        $status = 'prévu';
        
        // Vérifier si le patient a déjà un rendez-vous le même jour
        $sql = "SELECT * FROM appointments WHERE nom='$nom' AND prenom='$prenom' AND DATE(date) = DATE('$date')";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $message = "Vous avez déjà un rendez-vous ce jour-là.";
            $message_type = "error";
        } else {
            $message = addAppointment($nom, $prenom, $date_naissance, $maladie, $doctor_id, $date, $telephone, $email, $status);
            $message_type = "success";
        }
    }
}

function addAppointment($nom, $prenom, $date_naissance, $maladie, $doctor_id, $date, $telephone, $email, $status) {
    global $conn;
    $sql = "INSERT INTO appointments (nom, prenom, date_naissance, maladie, doctor_id, date, telephone, email, status) 
            VALUES ('$nom', '$prenom', '$date_naissance', '$maladie', $doctor_id, '$date', '$telephone', '$email', '$status')";
    if ($conn->query($sql) === TRUE) {
        return "Rendez-vous ajouté avec succès!";
    } else {
        return "Erreur: " . $conn->error;
    }
}

function getDoctors() {
    global $conn;
    $sql = "SELECT id, username FROM users WHERE role_id = (SELECT id FROM roles WHERE role_name = 'medecin')";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$doctors = getDoctors();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prendre un Rendez-vous</title>
    <link rel="stylesheet" href="rendezvouspatient.css">
</head>
<body>
    <header class="rendezvous-header">
        <div class="banner">
            <h1>Prendre un Rendez-vous</h1>
            <nav>
                <ul>
                    <li><a href="clinique.php">Retour à la page principale</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <?php if ($message): ?>
            <p class="message <?php echo $message_type; ?>"><?php echo $message; ?></p>
        <?php endif; ?>
        <section class="appointment-form">
            <h3>Formulaire de Rendez-vous</h3>
            <form method="POST" class="form-container">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" required>
                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" required>
                <label for="date_naissance">Date de naissance:</label>
                <input type="date" id="date_naissance" name="date_naissance" required>
                <label for="maladie">Choix de la maladie:</label>
                <select id="maladie" name="maladie" required>
                    <option value="Consultation et diagnostic dentaire">Consultation et diagnostic dentaire</option>
                    <option value="Détartrage et polissage">Détartrage et polissage</option>
                    <option value="Soins des caries">Soins des caries</option>
                    <option value="Conseils en hygiène bucco-dentaire">Conseils en hygiène bucco-dentaire</option>
                    <option value="Blanchiment dentaire">Blanchiment dentaire</option>
                    <option value="Facettes dentaires">Facettes dentaires</option>
                    <option value="Contouring dentaire">Contouring dentaire</option>
                    <option value="Traitement des gencives">Traitement des gencives</option>
                    <option value="Curetage gingival">Curetage gingival</option>
                    <option value="Appareils orthodontiques">Appareils orthodontiques</option>
                    <option value="Mainteneurs d'espace">Mainteneurs d'espace</option>
                    <option value="Prothèses dentaires">Prothèses dentaires</option>
                    <option value="Implants dentaires">Implants dentaires</option>
                    <option value="Traitement de canal (dévitalisation)">Traitement de canal (dévitalisation)</option>
                    <option value="Extraction dentaire">Extraction dentaire</option>
                    <option value="Chirurgie implantaire">Chirurgie implantaire</option>
                    <option value="Chirurgie corrective">Chirurgie corrective</option>
                    <option value="Consultations pour enfants">Consultations pour enfants</option>
                    <option value="Application de fluor">Application de fluor</option>
                    <option value="Scellement des sillons">Scellement des sillons</option>
                    <option value="Traitement des douleurs dentaires">Traitement des douleurs dentaires</option>
                    <option value="Réparation des dents cassées">Réparation des dents cassées</option>
                </select>
                <label for="doctor_id">Médecin:</label>
                <select id="doctor_id" name="doctor_id" required>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?php echo $doctor['id']; ?>"><?php echo $doctor['username']; ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="date">Date et heure préférée:</label>
                <input type="datetime-local" id="date" name="date" required>
                <label for="telephone">Numéro de téléphone:</label>
                <input type="tel" id="telephone" name="telephone" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <button type="submit" name="add_appointment">Prendre rendez-vous</button>
            </form>
        </section>
    </main>
</body>
</html>