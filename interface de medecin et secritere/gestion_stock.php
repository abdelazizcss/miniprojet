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
$message_class = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_stock'])) {
        $name = $_POST['name'];
        $quantity = $_POST['quantity'];
        $seuil_min = $_POST['seuil_min'];
        $expiration_date = $_POST['expiration_date'];
        $supplier = $_POST['supplier'];
        $status = $_POST['status'];
        $purchase_order = $_FILES['purchase_order']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["purchase_order"]["name"]);

        // Vérifier si le fichier est une image ou un PDF
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "pdf") {
            $message = "Désolé, seuls les fichiers JPG, JPEG, PNG et PDF sont autorisés.";
            $message_class = "error";
        } else {
            if (move_uploaded_file($_FILES["purchase_order"]["tmp_name"], $target_file)) {
                $message = addStock($name, $quantity, $seuil_min, $expiration_date, $supplier, $status, $purchase_order);
                $message_class = "success";
            } else {
                $message = "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
                $message_class = "error";
            }
        }
    } elseif (isset($_POST['delete_stock'])) {
        $id = $_POST['id'];
        $message = deleteStock($id);
        $message_class = strpos($message, 'succès') !== false ? 'success' : 'error';
    } elseif (isset($_POST['increase_quantity'])) {
        $id = $_POST['id'];
        $quantity = $_POST['quantity'];
        $message = updateStockQuantity($id, $quantity, 'increase');
        $message_class = strpos($message, 'succès') !== false ? 'success' : 'error';
    } elseif (isset($_POST['decrease_quantity'])) {
        $id = $_POST['id'];
        $quantity = $_POST['quantity'];
        $message = updateStockQuantity($id, $quantity, 'decrease');
        $message_class = strpos($message, 'succès') !== false ? 'success' : 'error';
    }
}

function getStock() {
    global $conn;
    $sql = "SELECT * FROM stock";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addStock($name, $quantity, $seuil_min, $expiration_date, $supplier, $status, $purchase_order) {
    global $conn;
    $sql = "INSERT INTO stock (name, quantity, seuil_min, expiration_date, supplier, status, purchase_order) VALUES ('$name', $quantity, $seuil_min, '$expiration_date', '$supplier', '$status', '$purchase_order')";
    if ($conn->query($sql) === TRUE) {
        return "Produit ajouté avec succès!";
    } else {
        return "Erreur: " . $conn->error;
    }
}

function deleteStock($id) {
    global $conn;
    $sql = "DELETE FROM stock WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return "Produit supprimé avec succès!";
    } else {
        return "Erreur: " . $conn->error;
    }
}

function updateStockQuantity($id, $quantity, $action) {
    global $conn;
    if ($action == 'increase') {
        $sql = "UPDATE stock SET quantity = quantity + $quantity WHERE id=$id";
    } else {
        $sql = "UPDATE stock SET quantity = quantity - $quantity WHERE id=$id";
    }
    if ($conn->query($sql) === TRUE) {
        return "Quantité mise à jour avec succès!";
    } else {
        return "Erreur: " . $conn->error;
    }
}

$stock = getStock();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Stock</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Gestion du Stock</h1>
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
            <p class="<?php echo $message_class; ?>"><?php echo $message; ?></p>
        <?php endif; ?>
        <h3>Ajouter un produit</h3>
        <form method="POST" class="form-container" enctype="multipart/form-data">
            <label for="name">Nom du produit:</label>
            <input type="text" id="name" name="name" required>
            <label for="quantity">Quantité:</label>
            <input type="number" id="quantity" name="quantity" required>
            <label for="seuil_min">Quantité minimale:</label>
            <input type="number" id="seuil_min" name="seuil_min" required>
            <label for="expiration_date">Date d'expiration:</label>
            <input type="date" id="expiration_date" name="expiration_date" required>
            <label for="supplier">Nom du fournisseur:</label>
            <input type="text" id="supplier" name="supplier" required>
            <label for="purchase_order">Bon d'achat (PDF ou image):</label>
            <input type="file" id="purchase_order" name="purchase_order" accept=".pdf, .jpg, .jpeg, .png" required>
            <label for="status">État du produit:</label>
            <select id="status" name="status" required>
                <option value="disponible">Disponible</option>
                <option value="demander">Demander</option>
            </select>
            <button type="submit" name="add_stock">Ajouter</button>
        </form>
        <h3>Stock actuel</h3>
        <table class="table-container">
            <tr>
                <th>Nom du produit</th>
                <th>Quantité</th>
                <th>Quantité minimale</th>
                <th>Date d'expiration</th>
                <th>Nom du fournisseur</th>
                <th>Bon d'achat</th>
                <th>État du produit</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($stock as $item): ?>
                <tr <?php if ($item['quantity'] < $item['seuil_min']) echo 'style="background-color: #f8d7da;"'; ?>>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo $item['seuil_min']; ?></td>
                    <td><?php echo $item['expiration_date']; ?></td>
                    <td><?php echo $item['supplier']; ?></td>
                    <td><a href="uploads/<?php echo $item['purchase_order']; ?>" target="_blank">Voir</a></td>
                    <td><?php echo $item['status']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <input type="number" name="quantity" required>
                            <button type="submit" name="increase_quantity">Augmenter</button>
                            <button type="submit" name="decrease_quantity">Diminuer</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="delete_stock">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>