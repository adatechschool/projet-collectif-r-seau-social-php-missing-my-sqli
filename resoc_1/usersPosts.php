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
         * Etape 1: Le mur concerne un utilisateur en particulier
         * La première étape est donc de trouver quel est l'id de l'utilisateur
         * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
         * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
         * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
         */

        // include 'user.php';
        ?>
        <?php
        /**
         * Etape 2: se connecter à la base de donnée
         */
        include 'getDataBase.php';


        ?>

        <aside>
            <!-- <?php
            /**
             * Etape 3: récupérer le nom de l'utilisateur
             */
            $laQuestionEnSql = "SELECT * FROM posts WHERE id= '$userId' ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            $user = $lesInformations->fetch_assoc();
            //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
            //echo "<pre>" . print_r($user, 1) . "</pre>";
            ?> -->
            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez tous les message de l'utilisatrice :
                    (n°
                    <?php echo $userId ?>)
                </p>

                <form method="post" action="">
                    <?php
                    // Check if user is not logged in
                    if (!isset($_SESSION['connected_id'])) {
                    ?>
                    <!-- Display button for not logged in user -->
                     <input type="submit" name="submit" value="Suivre">
                    <?php } else {
                        // Check if the user is following
                        $followed_user_id = $_GET['user_id'];
                        $following_user_id = $_SESSION['connected_id'];
                        $checkFollowQuery = "SELECT COUNT(*) as count FROM followers WHERE followed_user_id = $followed_user_id AND following_user_id = $following_user_id";
                        $result = $mysqli->query($checkFollowQuery);
                        $row = $result->fetch_assoc();
                        $isFollowing = ($row['count'] > 0);

                        // Set button label based on follow status
                        $buttonLabel = ($isFollowing) ? "Désabonnement" : "Suivre";

                        // Display the follow/unfollow button
                        ?>
                        <input type="hidden" name="user_id" value="<?php echo $followed_user_id; ?>">
                        <input type="submit" name="submit" value="<?php echo $buttonLabel; ?>">
                    <?php } ?>
                </form>

                <?php
                // Check if the button has been clicked when not logged in
                if (isset($_POST['submit'])) {
                    if (!isset($_SESSION['connected_id'])) {
                        header('Location: login.php');
                        exit;
                    // If the button is clicked when logged in
                    } else {
                        // If user is following, unfollow them; otherwise, follow them
                        if ($isFollowing) {
                            // Unfollow
                            $deleteFollowQuery = "DELETE FROM followers WHERE followed_user_id = $followed_user_id AND following_user_id = $following_user_id";
                            $mysqli->query($deleteFollowQuery);
                        } else {
                            // Follow
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
             * Etape 3: récupérer tous les messages de l'utilisatrice
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
             * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
             */
            while ($post = $lesInformations->fetch_assoc()) {

                // echo "<pre>" . print_r($post, 1) . "</pre>";
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
                        $tags = explode(',', $post['taglist']); // Explode the taglist into an array of tags
                        $tagIDs = explode(',', $post['tag_ids']);
                        $totalTags = count($tags); // Get the total number of tags
                        foreach ($tags as $index => $tag) {
                            // Trim each tag to remove any leading or trailing spaces
                            $tag = trim($tag);
                            // Display each tag preceded by #
                            echo '<a href="tags.php?tag_id=' . $tagIDs[$index] . '">#' . $tag . '</a>';
                            // Append a comma if it's not the last tag
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