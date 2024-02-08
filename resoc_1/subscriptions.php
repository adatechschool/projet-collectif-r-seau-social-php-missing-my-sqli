<?php
    include 'header.php';
    include 'checkConnection.php';
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Mes abonnements</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div id="wrapper">
        <aside>
            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez la liste des personnes dont
                    l'utilisatrice
                    n°
                    <?php echo intval($_SESSION['connected_id']) ?>
                    suit les messages
                </p>

            </section>
        </aside>
        <main class='contacts'>
            <?php
            // Etape 1: récupérer l'id de l'utilisateur
            // Etape 2: se connecter à la base de donnée
            include 'getDataBase.php';
            // Etape 3: récupérer le nom de l'utilisateur
            $laQuestionEnSql = "
                    SELECT users.* 
                    FROM followers 
                    LEFT JOIN users ON users.id=followers.followed_user_id 
                    WHERE followers.following_user_id='$userId'
                    GROUP BY users.id
                    ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            // Etape 4: à vous de jouer
            //@todo: faire la boucle while de parcours des abonnés et mettre les bonnes valeurs ci dessous 
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }

            while ($user = $lesInformations->fetch_assoc()) {
            
            ?>
            <article>
                <img src="user.jpg" alt="blason" />
                <h3><a href="wall.php?user_id=<?php echo $user['id'] ?>"><?php echo $user['alias'] ?></a></h3>
                <p><?php echo $user['id'] ?></p>
            </article>

            <?php
            }
            ;
            // et de pas oublier de fermer ici vote while
            ?>
        </main>
    </div>
</body>

</html>