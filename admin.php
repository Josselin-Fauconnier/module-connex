<?php
session_start();


$conn = new mysqli("localhost", "root", "root", "moduleconnexion");
if ($conn->connect_error) {
    exit("Erreur de connexion : " . $conn->connect_error);
}
$conn->set_charset("utf8");

$result = $conn->query("SELECT id, login, prenom, nom FROM utilisateurs ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Administration</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Glass+Antiqua&display=swap" rel="stylesheet">
</head>
<body>
<header class="contient_head">
    <h1>Administration</h1>
    <nav>
        <button><a href="index.php">Accueil</a></button>
        <button><a href="profil.php">Mon profil</a></button>
        <button><a href="deconnexion.php">Déconnexion</a></button>
    </nav>
</header>

<main>
    <div class="formulaire-conteneur">
        <h2>Utilisateurs enregistrés</h2>
        <table >
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Login</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['login']); ?></td>
                        <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($user['nom']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>

<?php $conn->close(); ?>
