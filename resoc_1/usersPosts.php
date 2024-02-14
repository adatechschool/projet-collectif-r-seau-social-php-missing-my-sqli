<?php
include 'header.php';
include 'user.php';
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
        /**
         * Récupère l'id de l'utilisatrice
         */


        ?>
        <?php
        /**
         * Se connecte à la base de donnée
         */
        include 'getDataBase.php';


        ?>

        <aside>
            <?php
            /**
             * Récupère le nom de l'utilisatrice
             */
            $laQuestionEnSql = "SELECT * FROM posts WHERE id= '$userId' ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            $user = $lesInformations->fetch_assoc();
            //Affiche le résultat
            ?>
            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez tous les message de l'utilisatrice :
                    (n°
                    <?php echo $userId ?>)
                </p>

                <form method="post" action="">
                    <?php
                    // Vérifie si l'utilisatrice n'est pas connectée
                    if (!isset($_SESSION['connected_id'])) {
                        ?>
                        <!-- Display button for not logged in user -->
                        <input type="submit" name="submit" value="Suivre">
                    <?php } else {
                        // Vérifie si l'utilisatrice est abonnée
                        $followed_user_id = $_GET['user_id'];
                        $following_user_id = $_SESSION['connected_id'];
                        $checkFollowQuery = "SELECT COUNT(*) as count FROM followers WHERE followed_user_id = $followed_user_id AND following_user_id = $following_user_id";

                        $result = $mysqli->query($checkFollowQuery);

                        $row = $result->fetch_assoc();

                        $isFollowing = ($row['count'] > 0);
                        echo $isFollowing;

                        // Définit le nom du bouton en fonction de son statut
                        $buttonLabel = ($isFollowing) ? "Désabonnement" : "Suivre";
                        echo $buttonLabel;

                        // Affiche le bouton
                        ?>
                        <input type="hidden" name="user_id" value="<?php echo $followed_user_id; ?>">
                        <input type="submit" name="submit" value="<?php echo $buttonLabel; ?>">
                    <?php } ?>
                </form>

                <?php
                // Vérifie si l'utilisatrice a cliqué en étant hors connexion
                if (isset($_POST['submit'])) {
                    if (!isset($_SESSION['connected_id'])) {
                        header('Location: login.php');
                        exit;
                    // If the button is clicked when logged in
                    } else {
                        // Si l'utilisatrice est abonnée : se désabonne, si elle n'est pas abonnée : s'abonne
                        if ($isFollowing) {
                            // Se désabonne
                            $deleteFollowQuery = "DELETE FROM followers WHERE followed_user_id = $followed_user_id AND following_user_id = $following_user_id";
                            $mysqli->query($deleteFollowQuery);
                        } else {
                            // S'abonne
                            $insertFollowQuery = "INSERT INTO followers (id, followed_user_id, following_user_id) VALUES (NULL, $followed_user_id, $following_user_id)";
                            $mysqli->query($insertFollowQuery);
                        }
                        header("Location: usersPosts.php?user_id=$followed_user_id");
                        exit;
                    }
                }
                ?>



            </section>
        </aside>
        <main>


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
             * Parcourt les messsages et remplit correctement le HTML avec les bonnes valeurs php
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