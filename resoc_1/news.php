<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Émotions accueil</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <?php
    include 'header.php';
    ?>

    <div class="center">
        <?php
        // Se connecte avec la base de données
        include 'getDataBase.php';
        // Vérifie
        if ($mysqli->connect_errno) {
            echo "<article>";
            echo ("Échec de la connexion : " . $mysqli->connect_error);
            echo ("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
            echo "</article>";
            exit();
        }

        // Interroge la base de données
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
                LIMIT 20
                ";
        $lesInformations = $mysqli->query($laQuestionEnSql);
        // Vérifie
        if (!$lesInformations) {
            echo "<article>";
            echo ("Échec de la requete : " . $mysqli->error);
            echo ("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
            exit();
        }

        // Parcourt ces données et les range dans le html




        while ($post = $lesInformations->fetch_assoc()) {

            $postID = $post['id'];

            ?>
            <!-- Affiche les articles -->
            <div class="post">
                <div class="post-content">
                    <div class="post-details">
                        <a class="post-author" href="usersPosts.php?user_id=<?php echo $post['user_id'] ?>"><?php echo $post['author_name'] ?></a>
                        <time class="post-date"><?php echo $post['created'] ?></time>
                    </div>
                    <p class="post-message"><?php echo $post['content'] ?></p>
                </div>
                <div class="post-bottom">
                    <div class="post-tag">
                    <p><?php
                    $tags = explode(',', $post['taglist']); // Sépare les tags dans un tableau
                    $tagIDs = explode(',', $post['tag_ids']);
                    $totalTags = count($tags); // Récupère le nombre total de tags
                    foreach ($tags as $index => $tag) {
                        // Remets en forme chaque tag
                        $tag = trim($tag);
                        // Affiche chaque tag précédé d'un #
                        echo '<a href="tags.php?tag_id=' . $tagIDs[$index] . '">#' . $tag . '</a>';
                        // Ajoute une virgule si ce n'est pas le dernier tag
                        if ($index < $totalTags - 1) {
                            echo ', ';
                        }
                    }
                    ?></p>
                    </div>
                    <div class="post-tag">
                    ♥
                        <?php echo $post['like_number'] ?>
                        <!-- Ajoute le bouton like -->
                        <?php include 'likeButton.php';
                        ?>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</body>

</html>