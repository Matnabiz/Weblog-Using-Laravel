<?php

require_once '../app/controllers/TagController.php';

$controller = new TagController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->getTagsWithPosts();
} else {
    header("HTTP/1.1 405 Method Not Allowed");
}
