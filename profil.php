<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: connexion.php");
    exit();
}


$conn = new mysqli("localhost", "root", "root", "moduleconnexion");
if ($conn->connect_error) {
    exit("Connexion échouée : " . $conn->connect_error);
}
$conn->set_charset("utf8");

$userId = $_SESSION['user']['id'];
$message = "";


$stmt = $conn->prepare("SELECT login, prenom, nom FROM utilisateurs WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmePassword = $_POST['confirme_password'] ?? '';

    if (empty($login) || empty($prenom) || empty($nom)) {
        $message = "Tous les champs (sauf mot de passe) sont obligatoires.";
    } elseif (!empty($password) && $password !== $confirmePassword) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        $check = $conn->prepare("SELECT id FROM utilisateurs WHERE login = ? AND id != ?");
        $check->bind_param("si", $login, $userId);
        $check->execute();
        $checkResult = $check->get_result();
        if ($checkResult->num_rows > 0) {
            $message = "Ce login est déjà utilisé.";
        } else {
            if (!empty($password)) {
                $hashPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE utilisateurs SET login = ?, prenom = ?, nom = ?, password = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $login, $prenom, $nom, $hashPassword, $userId);
            } else {
                $stmt = $conn->prepare("UPDATE utilisateurs SET login = ?, prenom = ?, nom = ? WHERE id = ?");
                $stmt->bind_param("sssi", $login, $prenom, $nom, $userId);
            }

            if ($stmt->execute()) {
                $message = "Le profil a été mis à jour.";
                $_SESSION['user']['login'] = $login;
                $_SESSION['user']['prenom'] = $prenom;
                $_SESSION['user']['nom'] = $nom;
                $user['login'] = $login;
                $user['prenom'] = $prenom;
                $user['nom'] = $nom;
            } else {
                $message = "Erreur lors de la mise à jour.";
            }
            $stmt->close();
        }
        $check->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil utilisateur</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Glass+Antiqua&display=swap" rel="stylesheet">
</head>
<body>
<header class="contient_head">
    <h1>Modifier mon profil</h1>
    <nav>
        <button><a href="index.php">Accueil</a></button>
        <button><a href="deconnexion.php">Déconnexion</a></button>
    </nav>
</header>

<main>
    <div class="formulaire-conteneur">
        <h2>Mes informations</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="formulaire-groupe">
                <label for="login">Login :</label>
                <input type="text" id="login" name="login" value="<?php echo htmlspecialchars($user['login']); ?>" required>
            </div>

            <div class="formulaire-groupe">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
            </div>

            <div class="formulaire-groupe">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
            </div>

            <div class="formulaire-groupe">
                <label for="password">Nouveau mot de passe :</label>
                <input type="password" id="password" name="password">
            </div>

            <div class="formulaire-groupe">
                <label for="confirme_password">Confirmer le nouveau mot de passe :</label>
                <input type="password" id="confirme_password" name="confirme_password">
            </div>

            <button type="submit" class="bouton_ins">Mettre à jour</button>
        </form>
    </div>
</main>
</body>
</html>
