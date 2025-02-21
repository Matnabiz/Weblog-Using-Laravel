<?php session_start();
define('BASE_PATH', realpath(__DIR__ . '/../' . '/../'));
require_once BASE_PATH.'/config/config.php';
require_once BASE_PATH.'/app/models/UserManager.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
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
        .signup {
            display: block;
            margin: 20px auto;
            text-align: center;
        }
        .signup a {
            color: #38369A;
            font-size: 18px;
        }
        label {
            text-align: center; 
            color: #38369A; 
            font-family: serif}
        form { 
            margin: 0 auto; 
            width:250px
        }
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    </style>
</head>
<body>
    
<div class = "container">
    <h1>Sign Up</h1>
    <form action="registration_form.php" method="post">

        <label for="first_name">First Name:</label><br>
        <input type="text" id="first_name" name="first_name" required><br><br>

        <label for="last_name">Last Name:</label><br>
        <input type="text" id="last_name" name="last_name" required><br><br>

        <label for="email">Email:</label><br>
        <input type="text" id="email" name="email" required><br><br>

        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="repeated_password">Re-enter Password:</label><br>
        <input type="password" id="repeated_password" name="repeated_password" required><br><br>

        <div class="signup">
        <a href="login.php">Already have an account?</a>
        </div>
        <div class="signup">
            <a href="login.php">Sign In!</a>
        </div>

        <div style="text-align:center">  
        <input type="submit" />  
        </div> 
    </form>
</div>


</body>
</html>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $repeatedPassword = $_POST['repeated_password'];

    $userRegistration = new UserManager($db);
    $userRegistration->signUp($firstName, $lastName, $email, $username, $password, $repeatedPassword);
}
?>