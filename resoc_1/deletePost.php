
<?php
session_start();
include 'getDataBase.php';
if (isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $post_id = intval($mysqli->real_escape_string($post_id));
    echo $post_id;

    $delete_posttag = "DELETE FROM posts_tags WHERE post_id = $post_id";
    $resulttag = $mysqli->query($delete_posttag);

    $delete_post = "DELETE FROM posts WHERE id = $post_id";
    // $delete_post ="DELETE posts, posts_tags
    // FROM posts
    // JOIN posts_tags ON posts.id = posts_tags.post_id
    // WHERE posts.id = $post_id";
    $result = $mysqli->query($delete_post);
    if ($result) {
        header('Location: wall.php');
        exit;
    } else {
        echo "Erreur lors de la suppression du message : " . $mysqli->error;
    }
} else {
    header('Location: wall.php');
    exit;
}
?>