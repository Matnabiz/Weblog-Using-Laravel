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
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #C6EBBE;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #A9DBB8;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
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
    </style>
</head>
<body>
<?php echo '<h1>' . $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . '</h1>'; ?>

<div class="container">
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <div class="actions">
        <a href="view_posts.php">All Posts</a>
        <a href="submit_post.php">New Post</a>
        <a href="user_posts.php">My Posts</a>
        <a href="search.php">Search</a>

    </div>

    <div class="logout">
        <a href="../views/login.php">Logout</a>
    </div>
</div>

</body>
</html>
