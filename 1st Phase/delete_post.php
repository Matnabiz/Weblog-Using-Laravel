<?php
require_once 'PostManager.php';

if (isset($_GET['title'])) {
    $title = urldecode($_GET['title']);
    $postManager = new PostManager('posts.csv');
    $postManager->deletePost($title);
    header('Location: user_posts.php');
    exit();
} else {
    echo "No post title specified!";
}
?>