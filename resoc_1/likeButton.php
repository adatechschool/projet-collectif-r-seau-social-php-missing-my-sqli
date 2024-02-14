<?php

include 'getDataBase.php';
?>

<!doctype html>
<html lang="fr">

<body>
    <form method="post" action="">
        <?php
        // Vérifie que l'utilisatrice n'est pas connectée
        if (!isset($_SESSION['connected_id'])) {
            ?>
            <!-- Affiche le bouton pour les utilisatrices hors connexion -->
            <input type="submit" name="" value="♥">
            <input type="hidden" name="id_post" value="<?php echo $liked_post_id; ?>">
            <!-- <input type="submit" name="submit" value="Like"> -->
        <?php } else {
            // Vérifie si l'utilisatrice a aimé le post 
            $liked_post_id = $postID;
            $current_user_id = $_SESSION['connected_id'];
            $check_like_query = "SELECT COUNT(*) as count FROM likes WHERE post_id = $liked_post_id AND user_id = $current_user_id";
            $result = $mysqli->query($check_like_query);
            $row = $result->fetch_assoc();
            $isLiked = ($row['count'] > 0);

            // Définit le bouton en fonction de son statut
            $buttonLabel = ($isLiked) ? "Dislike" : "Like";

            // Affiche le bouton Aimer/Ne pas aimer
            ?>
            <input type="submit" name="" value="♥">
            <input type="hidden" name="id_post" value="<?php echo $liked_post_id; ?>">
        <?php } ?>
    </form>
</body>


<?php

$id = $_POST["id_post"];

// Vérifie si on a cliqué hors connexion
if (isset($_POST['id_post'])) {

    if ($id == $liked_post_id) {

        // Envoie vers la page de connexion si on clique hors connexion
        if (!isset($_SESSION['connected_id'])) {
            header('Location: login.php');
            exit;
            // Exécute le code après avoir cliqué si on est connecté
        } else {
            if ($isLiked) {
                $deleteLikeQuery = "DELETE FROM likes WHERE post_id = $id AND user_id = $current_user_id";
                $mysqli->query($deleteLikeQuery);

            } else {
                $insertLikeQuery = "INSERT INTO likes (id, user_id, post_id) VALUES (NULL, $current_user_id, $id)";
                $mysqli->query($insertLikeQuery);

            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

}
?>