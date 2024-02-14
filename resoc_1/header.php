<?php
// Création de la session
session_start();


$userId = $_SESSION['connected_id'];
?>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="header.css" />
</head>

<header class="header">
    <nav class="menu">
        <a class="navlogo" href="news.php">Émotions</a>
        <a href="wall.php">Profil</a>
        <a href="feed.php">Flux</a>
        <a href="settings.php">Paramètres</a>
        <?php
            session_start();
            if (isset($_SESSION['connected_id'])) {
                echo '<a href="deconnexion.php">Déconnexion</a>';
            } else {
                echo '<a href="login.php">Connexion</a>';
            }
        ?>
    </nav>
</header>