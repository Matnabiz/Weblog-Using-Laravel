<?php
require_once 'PostManager.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['originalTitle']) && isset($_POST['title']) && isset($_POST['content'])) {
    $originalTitle = $_POST['originalTitle'];
    $newTitle = $_POST['title'];
    $newContent = $_POST['content'];

    $postManager = new PostManager('posts.csv');
    $postManager->updatePost($originalTitle, $newTitle, $newContent);

    header('Location: user_posts.php');
    exit();
} else {
    echo "Required data missing!";
}
?>
