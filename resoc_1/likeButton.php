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
            // echo "<h1>1</h1>";
            $liked_post_id = $_GET['post_id'];
            // echo "<h1>2!</h1>";
            $current_user_id = $_SESSION['connected_id'];
            // echo "<h1>3!</h1>";
            $check_like_query = "SELECT COUNT(*) as count FROM likes WHERE likes_post_id = $liked_post_id AND likes_user_id = $current_user_id";
            // echo "<h1>4!</h1>";
            $result = $mysqli->query($check_like_query);
            // echo "<h1>5!</h1>";
            // $row = $result->fetch_assoc();
            $row = mysqli_fetch_row($result);
            // echo "<h1>6!</h1>";
            $isLiked = ($row['count'] > 0);
            // echo "<h1>7!</h1>";

            // Set button label based on follow status
            $buttonLabel = ($isLiked) ? "Dislike" : "Like";

            // Display the follow/unfollow button
            ?>
            <input type="submit" name="submit" value="<?php echo $buttonLabel; ?>">
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