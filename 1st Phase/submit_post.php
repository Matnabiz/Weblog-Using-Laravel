<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Sharing Website</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #C6EBBE;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center; 
            color: #38369A; 
            font-family: serif; 
            style: bold
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #A9DBB8;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        form { 
            margin: 0 auto; 
            width:250px
        }

        label {
            text-align: center; 
            color: #38369A; 
            font-family: serif
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #38369A;
            color: #C6EBBE;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #95B6C6;
            color: #38369A;
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
        <a href="view_posts.php">All Posts</a>
        <a href="user_posts.php">My Posts</a>
    </div>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>
</div>
    <div class="container">
    <h1>Share a Post</h1>
    <form action="submit_post.php" method="post">

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Content:</label>
        <textarea id="content" name="content" rows="4" cols="50" required></textarea>

        <input type="submit" value="Submit">
    </form>
    </div>
</body>
</html>



<?php
$csvFile = 'posts.csv';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $file = fopen($csvFile, 'a');
    $username = $_SESSION['username'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $timestamp = date("Y-m-d H:i:s");


    fputcsv($file, [$username, $title, $content, $timestamp]);
    fclose($file);
    echo "Post saved successfully!";
}
?>