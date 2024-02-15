<?php
include 'header.php';
include 'checkConnection.php';
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Mon profil</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="wall.css" />
</head>

<body>
    <div class="center">
       <?php
        // Se connecter à la base de données
        include 'getDataBase.php';
        ?>
        <main>
            <article>

                <?php
                include 'getDataBase.php';

                // Vérifie si on traite ou affiche le formulaire
                
                $enCoursDeTraitement = isset($_POST['message']);



                if ($enCoursDeTraitement) {
                    // Récupère ce qu'il y a dans le formulaire
                    $authorId = $_SESSION['connected_id'];
                  

                    $postContent = $_POST['message'];
                    //Etape 3 : Petite sécurité
                    // pour éviter les injection sql : https://www.w3schools.com/sql/sql_injection.asp
                    $authorId = intval($mysqli->real_escape_string($authorId));
                    $postContent = $mysqli->real_escape_string($postContent);
                    //Etape 4 : construction de la requete

                    $tagLabel = $_POST['elements'];
                    $sql = "SELECT id FROM tags WHERE label = '$tagLabel'";
                    $resultat = $mysqli->query($sql);
                    $tag = $resultat->fetch_assoc();
                    $tagId = $tag['id'];

                    
                    echo "<pre>" . print_r($resultatpost) . "</pre>";

                    $lInstructionSql = "INSERT INTO posts "
                        . "(id, user_id, content, created) "
                        . "VALUES (NULL, "
                        . $authorId . ", "
                        . "'" . $postContent . "', "
                        . "NOW());"
                    ;
                    
                    // Etape 5 : execution
                    $ok = $mysqli->query($lInstructionSql);
                    if (!$ok) {
                        echo "Impossible d'ajouter le message: " . $mysqli->error;
                    }
                }
    
                $sql = "SELECT posts.id FROM posts WHERE content = '$postContent'";
                $resultatpost = $mysqli->query($sql);
                $post = $resultatpost->fetch_assoc();
                $postId = $post['id'];
                            
                
                $lInstructionSqlposttag ="INSERT INTO posts_tags "
                . "(id, post_id, tag_id) "
                . "VALUES (NULL, "
                .  $postId . ", "
                . "'" . $tagId . "')"
                ;
                $okpost = $mysqli->query($lInstructionSqlposttag);
            //     if (!$okpost) {
            // echo "Impossible d'ajouter le tag au post: " . $mysqli->error;
            //     }

                ?>



                <!-- envoi du message -->
                <form action="wall.php" method="post">
                    <input type='hidden' name='???' value='achanger'>
                    <dl>
                        
                    <!-- <label for="elements">Sélectionnez un mot-clé :</label> -->
                    
                                
                        </select></dd>
                        <!-- <dt><label for='message'>Message</label></dt> -->
                        
                      
                    
                </form>
            </article>
            <div class="post">
                <textarea class="message-box" name='message' placeholder="écris ici..."></textarea>
                <div class="post-bottom">
                    <select class="tag-dropdown"name="elements" id="elements">
                        <option value="" disabled selected>choisi ton émotion ici...</option>
                        <?php
                        $sql = "SELECT * FROM tags  ";    
                        $resultat = $mysqli->query($sql);        
                        while ($tag = $resultat->fetch_assoc()) {
                            echo "<option value='" . $tag['label']  . "'>" . $tag['label']  . "</option>";
                        }
                        ?>
                    </select>
                    <input class="message-submit" value="Envoyer" type='submit'>
                </div>
            </div>
            <?php
            
            // Récupère tous les messages de l'utilisatrice
            
            $laQuestionEnSql = "
                    SELECT posts.content, 
                    posts.created, 
                    posts.id AS post_id,
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
            
            // Parcourt tous les messsages et remplit correctement le HTML avec les bonnes valeurs php
            
            while ($post = $lesInformations->fetch_assoc()) {
                ?>
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
                    <form method="post" action="deletePost.php">
                        <input class="post-delete" type="submit" value="Supprimer">
                        <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                    </form>
                    <div class="post-tag">
                    ♥
                        <?php echo $post['like_number'] ?>
                        <!-- Ajoute le bouton like -->
                    </div>
                </div>
            </div>
            <?php } ?>


        </main>
    </div>
    <div class="background"></div>
</body>

</html>