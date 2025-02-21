<?php
define('DB_SERVER','localhost');
define('DB_USER','root');
define('DB_PASS' ,'');
define('DB_NAME', 'testdb');

$db = new mysqli(DB_SERVER, DB_USER, DB_PASS);

if (mysqli_connect_errno())
  exit('Connect failed: '. mysqli_connect_error());

$sql = "CREATE DATABASE IF NOT EXISTS `testdb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";

$db = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);

if ($db->connect_error)
    die("Connection failed: " . $db->connect_error);

$users_sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(30) NOT NULL,
    last_name VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, 
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$posts_sql = "CREATE TABLE IF NOT EXISTS posts (
    id INT(6) AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255),
    content LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$likes_sql = "CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
)";

$tags_sql = "CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
)";

$post_tags_sql = "CREATE TABLE IF NOT EXISTS post_tags (
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    PRIMARY KEY (post_id, tag_id)
)";

$db->query($users_sql);
$db->query($posts_sql);
$db->query($likes_sql);
$db->query($tags_sql);
$db->query($post_tags_sql);

?>
