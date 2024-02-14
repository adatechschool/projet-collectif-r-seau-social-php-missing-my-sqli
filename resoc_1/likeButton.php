<?php
session_start();
include 'getDataBase.php';
?>

<!doctype html>
<html lang="fr">

<body>
    <form method="post" action="">
        <?php
        // Check if user is not logged in
        if (!isset($_SESSION['connected_id'])) {
            ?>
            <!-- Display button for not logged in user -->
            <input type="submit" name="submit" value="Like">
        <?php } else {
            // Check if the user has liked the post
            $liked_post_id = $post['id'];
            $current_user_id = $_SESSION['connected_id'];
            $check_like_query = "SELECT COUNT(*) as count FROM likes WHERE post_id = $liked_post_id AND user_id = $current_user_id";
            // echo $liked_post_id;
            $result = $mysqli->query($check_like_query);
            $row = $result->fetch_assoc();
            // echo $row;
            $isLiked = ($row['count'] > 0);
            echo $isLiked;

            // Set button label based on follow status
            $buttonLabel = ($isLiked) ? "Dislike" : "Like";
            echo $buttonLabel;

            // Display the follow/unfollow button
            ?>
<<<<<<< HEAD
            <input type="submit" name="submit" id="<?php $liked_post_id ?>"
                value="<?php echo $liked_post_id, $buttonLabel; ?>">
=======
            <input type="submit" name="" value="♥">
            <input type="hidden" name="id_post" value="<?php echo $liked_post_id; ?>">
>>>>>>> a940d8f367ffc38c7e554d505f18f115adc8e2eb
        <?php } ?>
    </form>
</body>

<?php
$id = $_POST["id_post"];
// echo "Id of form" . $id;

echo "<pre>" . print_r($_POST) . "<pre>";
// Check if the button has been clicked when not logged in
<<<<<<< HEAD
if (isset($_POST['submit'])) {
    if (!isset($_SESSION['connected_id'])) {
        header('Location: login.php');
        exit;
        // If the button is clicked when logged in
    } else {
        // If user is following, unfollow them; otherwise, follow them
        if ($isLiked) {
            $deleteLikeQuery = "DELETE FROM likes WHERE post_id = $liked_post_id AND user_id = $current_user_id";
            $mysqli->query($deleteLikeQuery);
            echo $deleteLikeQuery;
=======
if (isset($_POST['id_post'])) {

    if($id == $liked_post_id) {

        // var_dump($_POST);
    
        if (!isset($_SESSION['connected_id'])) {
            header('Location: login.php');
            exit;
        // If the button is clicked when logged in
>>>>>>> a940d8f367ffc38c7e554d505f18f115adc8e2eb
        } else {
            if ($isLiked) {
                $deleteLikeQuery = "DELETE FROM likes WHERE post_id = $id AND user_id = $current_user_id";
                $mysqli->query($deleteLikeQuery);
                echo $deleteLikeQuery;
            } else {
                $insertLikeQuery = "INSERT INTO likes (id, user_id, post_id) VALUES (NULL, $current_user_id, $id)";
                $mysqli->query($insertLikeQuery);
                echo $insertLikeQuery;
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
    
}
?>