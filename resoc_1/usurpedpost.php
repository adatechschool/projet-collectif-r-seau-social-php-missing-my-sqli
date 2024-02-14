<?php
session_start();
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Post d'usurpateur</title>
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
            <p>Sur cette page on peut poster un message en se faisant
                passer pour quelqu'un d'autre</p>
        </aside>
        <main>
            <article>
                <h2>Poster un message</h2>
                <?php



                include 'getDataBase.php';

                /**
                 * Récupère la liste des auteures
                 */
                $listAuteurs = [];
                $laQuestionEnSql = "SELECT * FROM users";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                while ($user = $lesInformations->fetch_assoc()) {
                    $listAuteurs[$user['id']] = $user['alias'];
                }



                // Vérifie si on affiche ou traite le formulaire
                
                $enCoursDeTraitement = isset($_POST['auteur']);
                if ($enCoursDeTraitement) {
                    // Récupère ce qu'il y a dans le formulaire @todo: c'est là que votre travaille se situe
                
                    $authorId = $_POST['auteur'];
                    $postContent = $_POST['message'];



                    // Sécurise
                    $authorId = intval($mysqli->real_escape_string($authorId));
                    $postContent = $mysqli->real_escape_string($postContent);
                    //Construit la requête
                    $lInstructionSql = "INSERT INTO posts "
                        . "(id, user_id, content, created) "
                        . "VALUES (NULL, "
                        . $authorId . ", "
                        . "'" . $postContent . "', "
                        . "NOW());"
                    ;
                    echo $lInstructionSql;
                    // Exécute la requête
                    $ok = $mysqli->query($lInstructionSql);
                    if (!$ok) {
                        echo "Impossible d'ajouter le message: " . $mysqli->error;
                    } else {
                        echo "Message posté en tant que :" . $listAuteurs[$authorId];
                    }
                }
                ?>
                <form action="usurpedpost.php" method="post">
                    <input type='hidden' name='???' value='achanger'>
                    <dl>
                        <dt><label for='auteur'>Auteur</label></dt>
                        <dd><select name='auteur'>
                                <?php
                                foreach ($listAuteurs as $id => $alias)
                                    echo "<option value='$id'>$alias</option>";
                                ?>
                            </select></dd>
                        <dt><label for='message'>Message</label></dt>
                        <dd><textarea name='message'></textarea></dd>
                    </dl>
                    <input type='submit'>
                </form>
            </article>
        </main>
    </div>
</body>

</html>