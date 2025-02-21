<?php session_start(); ?>
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
            color: #38369A;
            background-color: #7CA5B8   ;
            border-radius: 5px;
            text-align: center;
        }
        .actions a:hover {
            background-color: #C6EBBE;
        }
        .logout {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .logout a {
            padding: 10px 20px;
            text-decoration: none;
            color: #38369A;
            background-color: #FA8072   ;
            border-radius: 5px;
            text-align: center;        
        }
        .logout a:hover {
            background-color: #C6EBBE;
        }

        * {
        margin: 0;
        padding: 0;
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
<a class="button" href="dashboard.php" style="--color: #38369A;">
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    Dashboard
  </a>
<div class="container">
    <h1><?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></h1>
    <div class="actions">
        <a href="submit_post.php">New Post</a>
        <a href="user_posts.php">My Posts</a>
    </div>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>
</div>
<h1>All Posts</h1>


<?php
$csvFile = 'posts.csv';
if (file_exists($csvFile)) {
    $file = fopen($csvFile, 'r');
    if(($row = fgetcsv($file)) == FALSE)
        echo '<p>No posts found!</p>';
    fclose($file);
    $file = fopen($csvFile, 'r');
    while (($row = fgetcsv($file)) !== FALSE) {
        echo '<div class="post">';
        echo '<h1>' . htmlspecialchars($row[1]) . '</h1>';
        echo '<p class="timestamp"> by ' . htmlspecialchars($row[0]) . ' at ' . htmlspecialchars($row[3]) . '</p>';
        echo '<p>' . nl2br(htmlspecialchars($row[2])) . '</p>';
        echo '</div>';
    }

    fclose($file);
} else {
    echo '<p>No file found!</p>';
}
?>

</body>
</html>
