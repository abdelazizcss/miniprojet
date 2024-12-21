<?php
$roles = getRoles();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir Rôles</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <table class="table-container">
        <tr><th>Rôle</th></tr>
        <?php
        foreach ($roles as $role) {
            echo "<tr><td>{$role['role_name']}</td></tr>";
        }
        ?>
    </table>
</body>
</html>