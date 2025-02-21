<?php
define('BASE_PATH', realpath(__DIR__ . '/../' . '/../'));
require_once BASE_PATH.'/app/models/TagManager.php';

class TagController {

    private $tagManager;

    public function __construct() {
        $this->tagManager = new TagManager();
    }

    public function getTagsWithPosts() {
        $tags = $this->tagManager->getAllTagsWithPosts();

        header('Content-Type: application/json');
        echo json_encode($tags);
    }
}
?>
