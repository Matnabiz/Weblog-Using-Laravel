<?php 
session_start();
define('BASE_PATH', realpath(__DIR__ . '/../' . '/../'));
require_once BASE_PATH.'/config/config.php';
require_once BASE_PATH.'/app/models/UserManager.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            font-family: serif; 
            style: bold
        }
        label {
            text-align: center; 
            color: #38369A; 
            font-family: serif; 
        }
        form { 
            margin: 0 auto; 
            width:250px
        }
        .signup {
            display: block;
            margin: 20px auto;
            text-align: center;
        }
        .signup a {
            color: #38369A;
            font-size: 18px;
        }
    
    </style>    

    
    <title>Post Sharing Website</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="container">
    <h1>Login</h1>
    <form action="login.php" method="post">
        <label for="email">Email:</label><br>
        <input type="text" id="email" name="email" required><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <div style="text-align:center">  
        <input type="submit" />  
        </div>      
        </form>
    <div class="signup">
        <a href="registration_form.php">Don't have an account?</a>
    </div>
    <div class="signup">
        <a href="registration_form.php">Sign Up!</a>
    </div>
</div>

</body>
</html>


<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_email = $_POST['email'];
    $entered_password = $_POST['password'];
    $userLogin = new UserManager($db);
    $userLogin->login($entered_email, $entered_password);
}
?>


