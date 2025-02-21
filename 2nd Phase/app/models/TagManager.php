<?php

class TagManager {

    private $conn;

    public function __construct() {
        define('BASE_ROOT_PATH', realpath(__DIR__ . '/../' . '/../'));
        require_once BASE_ROOT_PATH.'/config/config.php';
        $this->conn = $db;
    }

    public function getAllTagsWithPosts() {
        $sql = "SELECT tags.id AS tag_id, tags.name AS tag_name, COUNT(post_tags.post_id) AS post_count 
                FROM tags
                LEFT JOIN post_tags ON tags.id = post_tags.tag_id
                GROUP BY tags.id
                ORDER BY tags.name";

        $result = $this->conn->query($sql);
        $tagsWithPosts = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $tagsWithPostCount[] = [
                    'tag_id' => $row['tag_id'],
                    'tag_name' => $row['tag_name'],
                    'post_count' => $row['post_count']
                ];
            }
        }

        return $tagsWithPostCount;
    }
}
