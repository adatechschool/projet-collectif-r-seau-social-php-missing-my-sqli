<?php
session_start();
include 'getDataBase.php';
?>

<!doctype html>
<html lang="fr">

<body>
    <form method="post" action="">
        <?php
        // Vérifie si l'utilisatrice n'est pas connectée
        if (!isset($_SESSION['connected_id'])) {
            ?>
            <!-- Affiche le menu de connexion si l'utilisatrice n'est pas connectée -->
            <input type="submit" name="submit" value="Like">
        <?php } else {
            // Vérifie si l'utilisatrice a aimé le post
            $liked_post_id = $post['id'];
            $current_user_id = $_SESSION['connected_id'];
            $check_like_query = "SELECT COUNT(*) as count FROM likes WHERE post_id = $liked_post_id AND user_id = $current_user_id";
            $result = $mysqli->query($check_like_query);
            $row = $result->fetch_assoc();
            $isLiked = ($row['count'] > 0);


            // Ajoute le nom du bouton en fonction du statut : aimé ou non.
            $buttonLabel = ($isLiked) ? "Dislike" : "Like";


            // Affiche le bouton ou non 
            ?>
            <input type="submit" name="" value="♥">
            <input type="hidden" name="id_post" value="<?php echo $liked_post_id; ?>">
        <?php } ?>
    </form>
</body>

<?php
$id = $_POST["id_post"];


echo "<pre>" . print_r($_POST) . "<pre>";
// Vérifie si on a cliqué sur le bouton hors connexion
if (isset($_POST['id_post'])) {

    if ($id == $liked_post_id) {


        // Renvoie vers la page de connexion si non connectée
        if (!isset($_SESSION['connected_id'])) {
            header('Location: login.php');
            exit;
            // Envoie la requête si connecté
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