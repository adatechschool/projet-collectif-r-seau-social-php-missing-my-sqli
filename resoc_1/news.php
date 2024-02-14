<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Actualités</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <?php
    include 'header.php';
    ?>
    <div id="wrapper">
        <aside>
            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez les derniers messages de
                    tous les utilisatrices du site.</p>
            </section>
        </aside>
        <main>
            <?php

            // Se connecte à la database
            
            include 'getDataBase.php';
            //verification
            if ($mysqli->connect_errno) {
                echo "<article>";
                echo ("Échec de la connexion : " . $mysqli->connect_error);
                echo ("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
                echo "</article>";
                exit();
            }

            // Crée la requête de récupération des infos des articles dans la database
            $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    posts.user_id,
                    posts.id,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.id ORDER BY tags.label ASC) AS tag_ids,
                    GROUP_CONCAT(DISTINCT tags.label ORDER BY tags.label ASC) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    LIMIT 5
                    ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            // Vérifie  la requête
            if (!$lesInformations) {
                echo "<article>";
                echo ("Échec de la requete : " . $mysqli->error);
                echo ("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                exit();
            }

            // Crée une boucle d'affichage des articles.
            


            while ($post = $lesInformations->fetch_assoc()) {

                $postID = $post['id'];

                ?>
                <!-- Affiche les articles -->
                <article>
                    <h3>
                        <time>
                            <?php echo $post['created'] ?>
                        </time>
                    </h3>
                    <address><a href="usersPosts.php?user_id=<?php echo $post['user_id'] ?>">
                            <?php echo $post['author_name'] ?>
                        </a>
                    </address>
                    <div>
                        <p>
                            <?php echo $post['content'] ?>
                        </p>
                    </div>
                    <footer>
                        <small>♥
                            <?php echo $post['like_number'] ?>
                            <!-- Ajoute le bouton like -->

                            <body>
                                <form method="post" action="">
                                    <?php
                                    // Vérifie si l'utilisatrice est connectée
                                    if (!isset($_SESSION['connected_id'])) {
                                        ?>
                                        <!-- Affiche le bouton si l'utilisatrice n'est pas connectée -->
                                        <input type="submit" name="submit" value="Like">
                                    <?php } else {
                                        // Vérifie si l'utilisatrice a aimé le post
                                        $liked_post_id = $post['id'];
                                        $current_user_id = $_SESSION['connected_id'];
                                        $check_like_query = "SELECT COUNT(*) as count FROM likes WHERE post_id = $liked_post_id AND user_id = $current_user_id";



                                        $result = $mysqli->query($check_like_query);

                                        $row = $result->fetch_assoc();

                                        $isLiked = ($row['count'] > 0);


                                        // Affiche le nom du bouton en fonction du statut aimé ou non
                                        $buttonLabel = ($isLiked) ? "Dislike" : "Like";


                                        // Affiche le bouton aimé ou non
                                        ?>
                                        <input type="submit" name="submit" id="<?php $liked_post_id ?>"
                                            value="<?php echo  $buttonLabel; ?>">
                                    <?php } ?>
                                </form>
                            </body>



                        </small>
                        <!-- Ajoute les tags -->
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
                <?php
            }

            // Vérifie si l'utilisatrice a cliqué sur le bouton en étant hors connexion
            if (isset($_POST['submit'])) {
                if (!isset($_SESSION['connected_id'])) {
                    header('Location: login.php');
                    exit;
                    // Exécute code ci-dessous si connectée
                } else {
                    // Aime ou n'aime plus le message en fonction de son statut actuel
                    if ($isLiked) {
                        $deleteLikeQuery = "DELETE FROM likes WHERE post_id = $liked_post_id AND user_id = $current_user_id";
                        $mysqli->query($deleteLikeQuery);

                    } else {
                        $insertLikeQuery = "INSERT INTO likes (id, user_id, post_id) VALUES (NULL, $current_user_id, $liked_post_id)";
                        $mysqli->query($insertLikeQuery);

                    }
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                }
            }

            ?>


        </main>
    </div>
</body>

</html>