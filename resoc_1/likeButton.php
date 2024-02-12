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
            $liked_post_id = $postID;
            $current_user_id = $_SESSION['connected_id'];
            $check_like_query = "SELECT COUNT(*) as count FROM likes WHERE post_id = $liked_post_id AND user_id = $current_user_id";
            // $check_like_query = "SELECT COUNT(*) as count FROM likes WHERE post_id = 12 AND user_id = $current_user_id";
            echo $liked_post_id;
            // echo $current_user_id;
            // echo $check_like_query;
            $result = $mysqli->query($check_like_query);
            // echo $result;
            $row = $result->fetch_assoc();
            // echo $row;
            $isLiked = ($row['count'] > 0);
            echo $isLiked;

            // Set button label based on follow status
            $buttonLabel = ($isLiked) ? "Dislike" : "Like";
            echo $buttonLabel;

            // Display the follow/unfollow button
            ?>
            <input type="submit" name="submit" id="<?php $liked_post_id ?>" value="<?php echo $liked_post_id, $buttonLabel; ?>">
        <?php } ?>
    </form>
</body>

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
            $deleteLikeQuery = "DELETE FROM likes WHERE post_id = $liked_post_id AND user_id = $current_user_id";
            $mysqli->query($deleteLikeQuery);
            echo $deleteLikeQuery;
        } else {
            $insertLikeQuery = "INSERT INTO likes (id, user_id, post_id) VALUES (NULL, $current_user_id, $liked_post_id)";
            // $insertLikeQuery = "INSERT INTO likes (id, user_id, post_id) VALUES (NULL, $current_user_id, 9)";
            $mysqli->query($insertLikeQuery);
            echo $insertLikeQuery;
        }
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}
?>