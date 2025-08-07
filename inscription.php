<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmePassword = $_POST['confirme_password'] ?? '';

    if (empty($login) || empty($prenom) || empty($nom) || empty($password) || empty($confirmePassword)) {
        $message = "Tous les champs sont obligatoires.";
    } elseif ($password !== $confirmePassword) {
        $message = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 5) {
        $message = "Le mot de passe doit contenir au moins 5 caractères.";
    } else {
        $conn = new mysqli("localhost", "root", "root", "moduleconnexion");

        if ($conn->connect_error) {
            $message = "Erreur de connexion à la base de données : " . $conn->connect_error;
        } else {
            $conn->set_charset("utf8");

            $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE login = ?");
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = "Ce login est déjà utilisé.";
            } else {
                $hashPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO utilisateurs (login, prenom, nom, password) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $login, $prenom, $nom, $hashPassword);

                if ($stmt->execute()) {
                    header("Location: connexion.php");
                    exit();
                } else {
                    $message = "Erreur lors de l'inscription : " . $stmt->error;
                }
            }

            $stmt->close();
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Glass+Antiqua&display=swap" rel="stylesheet">
</head>
<body>
    <header class="contient_head">
        <h1>Inscription</h1>
        <nav>
            <button><a href="index.php">Accueil</a></button>
            <button><a href="connexion.php">Se connecter</a></button>
        </nav>
    </header>

    <main>
        <div class="formulaire-conteneur">
            <h2>Créer un compte</h2>

            <?php if (!empty($message)): ?>
                <div class="message">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="formulaire-groupe">
                    <label for="login">Login :</label>
                    <input type="text" id="login" name="login" value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>" required>
                </div>

                <div class="formulaire-groupe">
                    <label for="prenom">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>" required>
                </div>

                <div class="formulaire-groupe">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" required>
                </div>

                <div class="formulaire-groupe">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>
                    <small>Minimum 5 caractères</small>
                </div>

                <div class="formulaire-groupe">
                    <label for="confirme_password">Confirmation du mot de passe :</label>
                    <input type="password" id="confirme_password" name="confirme_password" required>
                </div>

                <button type="submit" class="bouton_ins">S'inscrire</button>
            </form>
        </div>
    </main>
</body>
</html>