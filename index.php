<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title> Accueil du site </title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Glass+Antiqua&display=swap" rel="stylesheet">
</head>
<body>
    <header class="contient_head">
        <h1> <?php
        if (isset($_SESSION['user'])) {
                echo "Bienvenue, " . htmlspecialchars($_SESSION['user']['login']);
            } else {
                echo "Bienvenue invité";
            }
            ?>
        </h1>
        <nav>
            <?php if (isset($_SESSION['user'])): ?>
               <button><a href="profil.php">Mon profil</a></button>
                <button><a href="deconnexion.php">Se déconnecter</a></button>
                <?php if ($_SESSION['user']['login'] === 'admin'): ?>
                   <button><a href="admin.php">Administration</a></button> 
                <?php endif; ?>
            <?php else: ?>
                <button><a href="inscription.php">S'inscrire</a></button>
                <button><a href="connexion.php">Se connecter</a></button>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <section class="flex">
            <div id="boite_presentation">
                 <h2>Présentation du site</h2>
            <p>Sur ce site, vous pourez partager vos lectures,vos avis et recommandations.</p>
            </div>
        </section>
    </main>


</body>
</html>