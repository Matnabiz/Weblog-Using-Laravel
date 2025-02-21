<?php
define('BASE_PATH', realpath(__DIR__ . '/../' . '/../'));
require_once BASE_PATH.'/app/models/PostManager.php';
session_start();


class UpdatePostController{

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function updatePost(){
        if (isset($_POST['originalTitle']) && 
        isset($_POST['title']) && 
        isset($_POST['content']) && 
        isset($_POST['originalTags'])) {

        $originalTitle = $_POST['originalTitle'];
        $newTitle = $_POST['title'];
        $newContent = $_POST['content'];
        $originalTags = $_POST['originalTags'];
        $newTags = $_POST['tags'];


        $postManager = new PostManager($this->conn);
        $postManager->updatePost($originalTitle, $newTitle, $newContent, $originalTags, $newTags);

        header('Location: ../views/user_posts.php');
        exit();
        }
        else
        echo "Required data missing!";
    }
}

$likePost = new UpdatePostController($db);
$likePost->updatePost();



?>
