<?php
define('BASE_PATH', realpath(__DIR__ . '/../' . '/../'));
require_once BASE_PATH.'/config/config.php';
require_once BASE_PATH.'/router/Router.php';
require_once BASE_PATH.'/app/controllers/PostController.php';
require_once BASE_PATH.'/app/controllers/UserController.php';

$router = new Router();

$router->get('/posts', function () use ($db) {
    $controller = new PostController($db);
    $controller->viewPosts();
});

$router->post('/posts/delete', function () use ($db) {
    $controller = new PostController($db);
    $controller->deletePost($_POST['id']);
});

$router->resolve();
