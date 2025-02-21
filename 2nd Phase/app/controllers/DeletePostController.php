<?php
define('BASE_PATH', realpath(__DIR__ . '/../' . '/../'));
require_once BASE_PATH.'/app/models/PostManager.php';
require_once BASE_PATH.'/config/config.php';


class DeletePostController{

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function deletePost(){
        $postID = intval($_GET['id']);
        $postManager = new PostManager($this->conn);
        $postManager->deletePost($postID);
        header('Location: ../views/user_posts.php');
        exit();
    }
}

$deleteController = new DeletePostController($db);
$deleteController->deletePost();

?>