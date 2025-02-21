<?php
define('BASE_ROOT_PATH', realpath(__DIR__ . '/../' . '/../'));
require_once BASE_ROOT_PATH.'/config/config.php';

class PostManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function search($toBeSearched) {
        $searchQuery = isset($toBeSearched) ? $toBeSearched : '';
        $sql = "SELECT posts.title, posts.content, posts.reg_date, users.username 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                WHERE users.username LIKE ? OR posts.title LIKE ? OR posts.content LIKE ? 
                ORDER BY posts.reg_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $searchTerm = '%' . $searchQuery . '%';
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public function getPosts() {
        $sql = "SELECT posts.id, posts.title, posts.content, posts.reg_date, users.username, 
                (SELECT GROUP_CONCAT(tags.name) 
                 FROM tags 
                 JOIN post_tags ON tags.id = post_tags.tag_id 
                 WHERE post_tags.post_id = posts.id) AS tags,
                (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                ORDER BY posts.reg_date DESC";
        $result = $this->conn->query($sql);
        return $result;
    }

    public function addLike($user_id, $post_id) {
        if (!$this->hasLiked($user_id, $post_id)) {
            $sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $post_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function removeLike($user_id, $post_id) {
        $sql = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $post_id);
        $stmt->execute();
        $stmt->close();
    }

    public function hasLiked($user_id, $post_id) {
        $sql = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $post_id);
        $stmt->execute();
        $stmt->store_result();
        $liked = $stmt->num_rows > 0;
        $stmt->close();
        return $liked;
    }

    public function getPostByTitle($title) {
        $sql = "SELECT posts.id, posts.title, posts.content, posts.reg_date, users.username,
                GROUP_CONCAT(tags.name SEPARATOR ', ') AS tags
                FROM posts
                JOIN users ON posts.user_id = users.id
                LEFT JOIN post_tags ON posts.id = post_tags.post_id
                LEFT JOIN tags ON post_tags.tag_id = tags.id
                WHERE posts.title = ?
                GROUP BY posts.id, posts.title, posts.content, posts.reg_date, users.username";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $title);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public function submitPost($user_id, $title, $content, $timestamp, $tags){
        $sql = "INSERT INTO posts (user_id, title, content, reg_date) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isss", $user_id, $title, $content, $timestamp);

        if ($stmt->execute())
            echo "Post saved successfully!";
        else
            echo "Error: " . $stmt->error;
        $post_id = $stmt->insert_id;
        $stmt->close();

        $this->addTagsToPost($post_id, $tags);
    }

    public function addTagsToPost($post_id, $tags) {
        foreach ($tags as $tag) {
            $tag_id = $this->getTagId($tag);
            if (!$tag_id) {
                $tag_id = $this->createTag($tag);
            }
            $this->associateTagWithPost($post_id, $tag_id);
        }
    }

    private function getTagId($tag) {
        $sql = "SELECT id FROM tags WHERE name = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $tag);
        $stmt->execute();
        $stmt->bind_result($tag_id);
        $stmt->fetch();
        $stmt->close();

        return $tag_id;
    }

    private function createTag($tag) {
        $sql = "INSERT INTO tags (name) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $tag);
        $stmt->execute();
        $tag_id = $stmt->insert_id;
        $stmt->close();

        return $tag_id;
    }

    private function associateTagWithPost($post_id, $tag_id) {
        $sql = "INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $post_id, $tag_id);
        $stmt->execute();
        $stmt->close();
    }

    public function updatePost($originalTitle, $newTitle, $newContent, $originalTags, $newTags) {
        $sql = "UPDATE posts SET title = ?, content = ? WHERE title = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $newTitle, $newContent, $originalTitle);

        if ($stmt->execute())
            $this->updatePostTags($newTitle, $newTags);
        else
            echo "Error updating post: " . $this->conn->error;
        $stmt->close();
    }

    public function deletePost($postID) {
        $sqlDeleteLikes = "DELETE FROM likes WHERE post_id = ?";
        $stmtDeleteLikes = $this->conn->prepare($sqlDeleteLikes);
        $stmtDeleteLikes->bind_param("i", $postID);
        $stmtDeleteLikes->execute();
        $stmtDeleteLikes->close();

        $sqlDeletePost = "DELETE FROM posts WHERE id = ?";
        $stmtDeletePost = $this->conn->prepare($sqlDeletePost);
        $stmtDeletePost->bind_param("i", $postID);

        if ($stmtDeletePost->execute()) {
            echo "Post deleted successfully!";
        } else {
            echo "Error deleting post: " . $this->conn->error;
        }
        
        $stmtDeletePost->close();
    }

    private function updatePostTags($postTitle, $newTags) {
        $sql = "SELECT id FROM posts WHERE title = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $postTitle);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();
        $postId = $post['id'];

        $sqlDeleteTags = "DELETE FROM post_tags WHERE post_id = ?";
        $stmtDelete = $this->conn->prepare($sqlDeleteTags);
        $stmtDelete->bind_param("i", $postId);
        $stmtDelete->execute();
        $stmtDelete->close();

        $tagsArray = array_map('trim', explode(',', $newTags));
        foreach ($tagsArray as $tag) {
            $sqlTagCheck = "SELECT id FROM tags WHERE name = ?";
            $stmtTagCheck = $this->conn->prepare($sqlTagCheck);
            $stmtTagCheck->bind_param("s", $tag);
            $stmtTagCheck->execute();
            $resultTagCheck = $stmtTagCheck->get_result();

            if ($resultTagCheck->num_rows == 0) {
                $sqlInsertTag = "INSERT INTO tags (name) VALUES (?)";
                $stmtInsertTag = $this->conn->prepare($sqlInsertTag);
                $stmtInsertTag->bind_param("s", $tag);
                $stmtInsertTag->execute();
                $tagId = $stmtInsertTag->insert_id;
                $stmtInsertTag->close();
            } else {
                $tagRow = $resultTagCheck->fetch_assoc();
                $tagId = $tagRow['id'];
            }

            $sqlInsertPostTag = "INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)";
            $stmtInsertPostTag = $this->conn->prepare($sqlInsertPostTag);
            $stmtInsertPostTag->bind_param("ii", $postId, $tagId);
            $stmtInsertPostTag->execute();
            $stmtInsertPostTag->close();
        }

        $stmtTagCheck->close();
    }

}
?>
