<?php
session_start();
define('BASE_PATH', realpath(__DIR__ . '/../' . '/../'));
require_once BASE_PATH.'/config/config.php';
require_once BASE_PATH.'/app/models/PostManager.php';




class LikeController{

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function likePost(){
        $postManager = new PostManager($this->conn);
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $action = $_GET['action'];
            $post_id = $_GET['post_id'];
            $user_id = $_SESSION['user_id'];

            if ($action === 'like')
                $postManager->addLike($user_id, $post_id);
            elseif ($action === 'unlike')
                $postManager->removeLike($user_id, $post_id);

            header("Location: ../views/view_posts.php");
            exit();
        }
    }
}

$likePost = new LikeController($db);
$likePost->likePost();


?>
