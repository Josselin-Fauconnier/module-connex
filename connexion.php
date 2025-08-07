<?php

session_start();

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $message = "Tous les champs sont obligatoires.";
    } else {
       
        $conn = new mysqli("localhost", "root", "root", "moduleconnexion");

        if ($conn->connect_error) {
            $message = "Erreur de connexion à la base de données : " . $conn->connect_error;
        } else {
            $conn->set_charset("utf8");

            $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'login' => $user['login'],
                        'prenom' => $user['prenom'],
                        'nom' => $user['nom'],
                    ];  
                    if (header("Location: index.php"));
                    exit();
                } else {
                    $message = "Mot de passe incorrect.";
                }
            } else {
                $message = "Login incorrect ou utilisateur inexistant.";
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
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Glass+Antiqua&display=swap" rel="stylesheet">
</head>
<body>
<header class="contient_head">
    <h1>Connexion</h1>
    <nav>
        <button><a href="index.php">Accueil</a></button>
        <button><a href="inscription.php">S'inscrire</a></button>
    </nav>
</header>

<main>
    <div class="formulaire-conteneur">
        <h2>Se connecter</h2>

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
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="bouton_ins">Se connecter</button>
        </form>
    </div>
</main>
</body>
</html>
