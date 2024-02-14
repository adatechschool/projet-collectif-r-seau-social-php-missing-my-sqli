<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Connexion</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>

    <?php
    include 'header.php';
    ?>

    <div id="wrapper">

        <aside>
            <h2>Présentation</h2>
            <p>Bienvenu sur notre réseau social.</p>
        </aside>
        <main>
            <article>
                <h2>Connexion</h2>
                <?php

                // Vérifie si on affiche ou traite un formulaire
                $enCoursDeTraitement = isset($_POST['email']);
                if ($enCoursDeTraitement) {
                    // Récupère les infos du formulaire
                    $emailAVerifier = $_POST['email'];
                    $passwdAVerifier = $_POST['motpasse'];

                    include 'getDataBase.php';

                    // Sécurise l'envoi de données
                    $emailAVerifier = $mysqli->real_escape_string($emailAVerifier);
                    $passwdAVerifier = $mysqli->real_escape_string($passwdAVerifier);
                    // Crypte le mot de passe
                    $passwdAVerifier = md5($passwdAVerifier);

                    // Construit la requête
                    $lInstructionSql = "SELECT * "
                        . "FROM users "
                        . "WHERE "
                        . "email LIKE '" . $emailAVerifier . "'"
                    ;
                    // Vérifie l'utilisatrice
                    $res = $mysqli->query($lInstructionSql);
                    $user = $res->fetch_assoc();
                    if (!$user or $user["password"] != $passwdAVerifier) {
                        echo "La connexion a échouée. ";

                    } else {
                        echo "Votre connexion est un succès : " . $user['alias'] . ".";
                        $_SESSION['connected_id'] = $user['id'];


                        // Redirige vers la page principale
                        header("Location: news.php");
                    }
                }
                ?>
                <form action="login.php" method="post">
                    <input type='hidden' name='???' value='achanger'>
                    <dl>
                        <dt><label for='email'>E-Mail</label></dt>
                        <dd><input type='email' name='email'></dd>
                        <dt><label for='motpasse'>Mot de passe</label></dt>
                        <dd><input type='password' name='motpasse'></dd>
                    </dl>
                    <input type='submit'>
                </form>
                <p>
                    Pas de compte?
                    <a href='registration.php'>Inscrivez-vous.</a>
                </p>

            </article>
        </main>
    </div>
</body>

</html>