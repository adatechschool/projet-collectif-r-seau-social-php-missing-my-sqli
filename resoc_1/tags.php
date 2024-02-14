<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Les message par mot-clé</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <?php
    include 'header.php';
    include 'user.php';
    ?>
    <div id="wrapper">
        <?php

        /**
         * Définit la variable "tag"
         */
        $tagId = intval($_GET['tag_id']);
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
             * Récupère le nom du tag
             */
            $laQuestionEnSql = "SELECT * FROM tags WHERE id= '$tagId' ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            $tag = $lesInformations->fetch_assoc();

            ?>
            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez les derniers messages comportant
                    le mot-clé
                    <?php echo $tag['label'] ?>
                    (n°
                    <?php echo $tagId ?>)
                </p>

            </section>
        </aside>
        <main>
            <?php
            /**
             * Récupère tous les messages avec un mot clé donné
             */
            $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    posts.user_id,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.id ORDER BY tags.label ASC) AS tag_ids,
                    GROUP_CONCAT(DISTINCT tags.label ORDER BY tags.label ASC) AS taglist  
                    FROM posts_tags as filter 
                    JOIN posts ON posts.id=filter.post_id
                    JOIN users ON users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE filter.tag_id = '$tagId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }

            /**
             * Parcoure les messsages et remplit correctement le HTML avec les bonnes valeurs php
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
                        <a href="usersPosts.php?user_id=<?php echo $post['user_id'] ?>">
                            <?php echo $post['author_name'] ?>
                        </a>
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