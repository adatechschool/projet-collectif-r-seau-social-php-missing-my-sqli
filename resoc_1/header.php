<?php
// Création de la session
session_start();


$userId = $_SESSION['connected_id'];
?>

<header>
    <!-- Création du menu -->
    <img src="resoc.jpg" alt="Logo de notre réseau social" />

    <nav id="menu">
        <a href="news.php">Actualités</a>
        <a href="wall.php">Mur</a>
        <a href="feed.php">Flux</a>
        <a href="tags.php?tag_id=1">Mots-clés</a>
    </nav>
    <nav id="user">
        <a href="#">Profil</a>
        <ul>
            <li><a href="settings.php">Paramètres</a></li>
            <li><a href="followers.php">Mes suiveurs</a></li>
            <li><a href="subscriptions.php">Mes abonnements</a></li>
        </ul>

    </nav>
</header>