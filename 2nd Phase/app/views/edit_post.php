<?php
define('BASE_PATH', realpath(__DIR__ . '/../' . '/../'));
require_once BASE_PATH.'/app/models/PostManager.php';
require_once BASE_PATH.'/config/config.php';

session_start();
if (isset($_GET['title'])) {
    $title = urldecode($_GET['title']);
    $postManager = new PostManager($db);
    $post = $postManager->getPostByTitle($title);

    if ($post) {
        $originalTitle = $post['title'];
        $originalTags = $post['tags'];
        $content = $post['content'];
    } else {
        echo "Post not found!";
        exit();
    }
} else {
    echo "No post title specified!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #C6EBBE;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #38369A;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #38369A;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        label {
            text-align: center; 
            color: #38369A; 
            font-family: serif; 
        }
        h1 {
            text-align: center;
            color: #38369A;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #B0C8D4;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .button {
            position: absolute;
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

        .button:hover {
        color: #C6EBBE;
        border: 4px solid rgba(0, 0, 0, 0);
        border-radius: 10px;
        }

        .button::before {
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

        .button:hover::before {
        transform: scale(1);
        transition-delay: 0.5s;
            0 0 30px var(--color),
            0 0 60px var(--color);
        }

        .button span {
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

        .button:hover span {
        opacity: 0;
        transition-delay: 0s;
        }
    </style>
</head>

<body>
    <a class="button" href="../views/user_posts.php" style="--color: #38369A;">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        Return
    </a>
    
    <?php echo '<h1>' . $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . '</h1>'; ?>
    <h1>Edit Post</h1>

    <div class ="container">

        <form action="../controllers/UpdatePostController.php" method="post">
            <input type="hidden" name="originalTitle" value="<?php echo htmlspecialchars($originalTitle); ?>">
            
            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($originalTitle); ?>" required><br><br>
            
            <label for="content">Content:</label><br>
            <textarea id="content" name="content" rows="4" required><?php echo htmlspecialchars($content); ?></textarea><br><br>
            
            <input type="hidden" name="originalTags" value="<?php echo htmlspecialchars($originalTags); ?>">

            <label for="tags">Tags:</label><br>
            <input type="text" id="tags" name="tags" value="<?php echo htmlspecialchars($originalTags); ?>" required><br><br>
            
            <div style="text-align:center">  
            <input type="submit" value="Update Post">
        </form>
    </div>
</body>
</html>
