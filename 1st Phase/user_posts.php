<?php
require_once 'PostManager.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
$postManager = new PostManager('posts.csv');

$posts = $postManager->getPosts();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Posts</title>
        <style>
            h1 {
                text-align: center; 
                color: #38369A
            }
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                background-color: #C6EBBE;
            }
            .container {
                max-width: 600px;
                margin: 50px auto;
                padding: 20px;
                background-color: #A9DBB8;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            .actions {
                display: flex;
                justify-content: space-between;
                margin-top: 20px;
                
            }
            .actions a {
                padding: 10px 20px;
                text-decoration: none;
                color: red;
                background-color: #7CA5B8   ;
                border-radius: 5px;
                text-align: center;
            }
            .post {
                background-color: #B0C8D4;
                border: 1px solid #38369A;
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 5px;
            }
            .post h2 {
                margin-top: 0;
                font-size: 24px;
                text-align: center;
                color: #38369A;
            }
            .post p {
                font-size: 20px;
                line-height: 1.5;
            }
            .timestamp {
                color: #38369A;
                font-size: 14px;
                margin-bottom: 15px;
                text-align: center;
            }
            .info, .success, .warning, .error, .validation {
                border: 1px solid;
                margin: 10px 0px;
                padding: 15px 10px 15px 50px;
                background-repeat: no-repeat;
                background-position: 10px center;
            }
            .info {
                color: #00529B;
                background-color: #A9DBB8;
                background-image: url('https://i.imgur.com/ilgqWuX.png');
                text-align: center;
            }
            .button1 {
                position: fixed;
                top: 20px;
                left: 20px;
                padding: 16px 20px;
                font-size: 1.5rem;
                color: var(--color);
                border: 4px solid #38369A;
                border-radius: 10px;
                text-decoration: none;
                text-transform: uppercase;
                letter-spacing: 0.1rem;
                transition: 0.5s;
                z-index: 1;
            }

            .button1:hover {
            color: #C6EBBE;
            border: 4px solid rgba(0, 0, 0, 0);
            border-radius: 10px;
            }

            .button1::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--color);
            z-index: -1;
            transform: scale(0);
            transition: 0.5s;
            }

            .button1:hover::before {
            transform: scale(1);
            transition-delay: 0.5s;
                0 0 30px var(--color),
                0 0 60px var(--color);
            }

            .button1 span {
            position: absolute;
            background: var(--color);
            pointer-events: none;
            border-radius: 10px;
                0 0 20px var(--color),
                0 0 30px var(--color),
                0 0 50px var(--color),
                0 0 100px var(--color);
            transition: 0.5s ease-in-out;
            transition-delay: 0.25s;
            }

            .button1:hover span {
            opacity: 0;
            transition-delay: 0s;
            }
            .button2 {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 16px 30px;
                font-size: 1.5rem;
                color: var(--color);
                border: 4px solid #38369A;
                border-radius: 10px;
                text-decoration: none;
                text-transform: uppercase;
                letter-spacing: 0.1rem;
                transition: 0.5s;
                z-index: 1;
            }

            .button2:hover {
            color: #C6EBBE;
            border: 4px solid rgba(0, 0, 0, 0);
            border-radius: 10px;
            }

            .button2::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--color);
            z-index: -1;
            transform: scale(0);
            transition: 0.5s;
            }

            .button2:hover::before {
            transform: scale(1);
            transition-delay: 0.5s;
                0 0 30px var(--color),
                0 0 60px var(--color);
            }

            .button2 span {
            position: absolute;
            background: var(--color);
            pointer-events: none;
            border-radius: 10px;
                0 0 20px var(--color),
                0 0 30px var(--color),
                0 0 50px var(--color),
                0 0 100px var(--color);
            transition: 0.5s ease-in-out;
            transition-delay: 0.25s;
            }

            .button2:hover span {
            opacity: 0;
            transition-delay: 0s;
            }
            .button3 {
                position: fixed;
                top: 20px;
                right: 300px;
                padding: 16px 30px;
                font-size: 1.5rem;
                color: var(--color);
                border: 4px solid #FA8072;
                border-radius: 10px;
                text-decoration: none;
                text-transform: uppercase;
                letter-spacing: 0.1rem;
                transition: 0.5s;
                z-index: 1;
            }

            .button3:hover {
            color: #C6EBBE;
            border: 4px solid rgba(0, 0, 0, 0);
            border-radius: 10px;
            }

            .button3::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--color);
            z-index: -1;
            transform: scale(0);
            transition: 0.5s;
            }

            .button3:hover::before {
            transform: scale(1);
            transition-delay: 0.5s;
                0 0 30px var(--color),
                0 0 60px var(--color);
            }

            .button3 span {
            position: absolute;
            background: var(--color);
            pointer-events: none;
            border-radius: 10px;
                0 0 20px var(--color),
                0 0 30px var(--color),
                0 0 50px var(--color),
                0 0 100px var(--color);
            transition: 0.5s ease-in-out;
            transition-delay: 0.25s;
            }
            .button3:hover span {
            opacity: 0;
            transition-delay: 0s;
            }
            .button4 {
                position: fixed;
                top: 20px;
                left: 300px;
                padding: 16px 20px;
                font-size: 1.5rem;
                color: var(--color);
                border: 4px solid #38369A;
                border-radius: 10px;
                text-decoration: none;
                text-transform: uppercase;
                letter-spacing: 0.1rem;
                transition: 0.5s;
                z-index: 1;
            }

            .button4:hover {
            color: #C6EBBE;
            border: 4px solid rgba(0, 0, 0, 0);
            border-radius: 10px;
            }

            .button4::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--color);
            z-index: -1;
            transform: scale(0);
            transition: 0.5s;
            }

            .button4:hover::before {
            transform: scale(1);
            transition-delay: 0.5s;
                0 0 30px var(--color),
                0 0 60px var(--color);
            }

            .button4 span {
            position: absolute;
            background: var(--color);
            pointer-events: none;
            border-radius: 10px;
                0 0 20px var(--color),
                0 0 30px var(--color),
                0 0 50px var(--color),
                0 0 100px var(--color);
            transition: 0.5s ease-in-out;
            transition-delay: 0.25s;
            }

            .button4:hover span {
            opacity: 0;
            transition-delay: 0s;
            }
        </style>
    </head>

    <body>
        <a class="button1" href="dashboard.php" style="--color: #38369A;">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            Dashboard
        </a>
        <a class="button4" href="submit_post.php" style="--color: #38369A;">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            New Post
        </a>
        <a class="button2" href="view_posts.php" style="--color: #38369A;">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            All Posts
        </a>
        <a class="button3" href="logout.php" style="--color: #FA8072;">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            Log Out
        </a>
        <?php echo '<h1>' . $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . '</h1>'; ?>
        <div class="container">
            <h1>My Posts</h1>
        </div>
        <?php
        if (empty($posts)) {
            echo '<p>No posts found!</p>';
        } else {
            $user_post_count = 0;
            foreach ($posts as $post) {
                if ($post[0] == $_SESSION['username']) {
                    echo '<div class="post">';
                    echo '<h2>' . htmlspecialchars($post[1]) . '</h2>';
                    echo '<p class="timestamp"> by ' . htmlspecialchars($post[0]) . ' at ' . htmlspecialchars($post[3]) . '</p>';
                    echo '<p>' . nl2br(htmlspecialchars($post[2])) . '</p>';
                    echo '<div class="actions">';
                    echo '<a href="edit_post.php?title=' . urlencode($post[1]) . '">Edit Post</a>';
                    echo '<a href="delete_post.php?title=' . urlencode($post[1]) . '" onclick="return confirm(\'Are you sure you want to delete this post?\');">Delete Post</a>';
                    echo '</div>';
                    echo '</div>';
                    $user_post_count ++;
                }
            }
            if(!$user_post_count)
                echo '<div class="info">You have not submitted any posts yet!</div>';
        }
        ?>
    </body>
</html>
