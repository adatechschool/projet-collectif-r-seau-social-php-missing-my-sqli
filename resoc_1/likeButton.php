<?php
session_start();
include 'getDataBase.php';

$laQuestionEnSql = "
    SELECT *
    FROM `likes`
    WHERE `user_id` = '{$_SESSION['connected_id']}'
    AND `post_id` = '{$_GET['post_id']}'
    ";

    $lesInformations = $mysqli->query($laQuestionEnSql);
    $likeDislike = $lesInformations->fetch_assoc();


?>


<form method="post" action="">
    <?php
    // Check if user is not logged in
    if (!isset($_SESSION['connected_id'])) {
    ?>
    <!-- Display button for not logged in user -->
        <input type="submit" name="submit" value="Like">
    <?php } else {
        // Check if the user has liked the post
        $liked_post_id = $_GET['post_id'];
        $current_user_id = $_SESSION['connected_id'];
        $check_like_query = "SELECT COUNT(*) as count FROM likes WHERE likes_post_id = $liked_post_id AND likes_user_id = $current_user_id";
        $result = $mysqli->query($check_like_query);
        $row = $result->fetch_assoc();
        $isLiked = ($row['count'] > 0);

        // Set button label based on follow status
        $buttonLabel = ($isLiked) ? "Dislike" : "Like";

        // Display the follow/unfollow button
        ?>
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
        if ($isLiked) {
            // Unfollow
            $deleteLikeQuery = "DELETE FROM likes WHERE likes_post_id = $liked_post_id AND likes_user_id = $current_user_id";
            $mysqli->query($deleteLikeQuery);
        } else {
            // Follow
            $insertLikeQuery = "INSERT INTO likes (id, likes_user_id, likes_post_id) VALUES (NULL, $current_user_id, $liked_post_id)";
            $mysqli->query($insertLikeQuery);
        }
        // header("Location: usersPosts.php?user_id=$followed_user_id");
        exit;
    }
}
?>