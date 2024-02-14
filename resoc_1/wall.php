<?php
include 'header.php';
include 'checkConnection.php';
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Mur</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>

    <div id="wrapper">
        <?php

        ?>
        <?php
        /**
         * Se connecte à la base de donnée
         */
        include 'getDataBase.php';


        ?>

        <aside>

            <!-- 
             * Récupère le nom de l'utilisatrice
             -->


            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez tous les message de l'utilisatrice :
                    (n°
                    <?php echo $userId ?>)
                </p>
            </section>
        </aside>
        <main>
            <article>
                <h2>Poster un message</h2>
                <?php



                include 'getDataBase.php';



                // Vérifie si on traite ou affiche le formulaire
                
                $enCoursDeTraitement = isset($_POST['message']);



                if ($enCoursDeTraitement) {
                    // Récupère ce qu'il y a dans le formulaire
                    $authorId = $_SESSION['connected_id'];
                    $postContent = $_POST['message'];


                    //Sécurise
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

                    $ok = $mysqli->query($lInstructionSql);
                    if (!$ok) {
                        echo "Impossible d'ajouter le message: " . $mysqli->error;
                    }

                }
                ?>
                <form action="wall.php" method="post">
                    <input type='hidden' name='???' value='achanger'>
                    <dl>

                        </select></dd>
                        <dt><label for='message'>Message</label></dt>
                        <dd><textarea name='message'></textarea></dd>
                    </dl>
                    <input type='submit'>
                </form>
            </article>

            <?php
            /**
             * Récupère tous les messages de l'utilisatrice
             */
            $laQuestionEnSql = "
                    SELECT posts.content, 
                    posts.created, 
                    users.alias as author_name, 
                    COUNT(likes.id) as like_number, 
                    GROUP_CONCAT(DISTINCT tags.id ORDER BY tags.label ASC) AS tag_ids,
                    GROUP_CONCAT(DISTINCT tags.label ORDER BY tags.label ASC) AS taglist                     FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE posts.user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }

            /**
             * Parcourt tous les messsages et remplit correctement le HTML avec les bonnes valeurs php
             */
            while ($post = $lesInformations->fetch_assoc()) {


                ?>
                <article>
                    <h3>
                        <time datetime='2020-02-01 11:12:13'>
                            <?php echo $post['created'] ?>
                        </time>
                    </h3>
                    <address>
                        <?php echo $post['author_name'] ?>
                    </address>
                    <div>
                        <?php echo $post['content'] ?>
                    </div>
                    <footer>
                        <small>♥
                            <?php echo $post['like_number'] ?>
                        </small>

                        <?php
                        $tags = explode(',', $post['taglist']);
                        $tagIDs = explode(',', $post['tag_ids']);
                        $totalTags = count($tags);
                        foreach ($tags as $index => $tag) {
                            $tag = trim($tag);
                            echo '<a href="tags.php?tag_id=' . $tagIDs[$index] . '">#' . $tag . '</a>';
                            if ($index < $totalTags - 1) {
                                echo ', ';
                            }
                        }
                        ?>

                    </footer>
                </article>
            <?php } ?>


        </main>
    </div>
</body>

</html>