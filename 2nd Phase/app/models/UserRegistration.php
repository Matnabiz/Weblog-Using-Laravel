<?php
define('BASE_ROOT_PATH', realpath(__DIR__ . '/../' . '/../'));
require_once BASE_PATH.'/config/config.php';
session_start();
class UserRegistration 
{
    private $conn;
    private $firstName;
    private $lastName;
    private $email;
    private $username;
    private $password;
    private $repeatedPassword;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getUserByEmail(){
        $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->entered_email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public function signUp($firstName, $lastName, $email, $username, $password, $repeatedPassword){
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->repeatedPassword = $repeatedPassword;

        if ($this->usernameExists()) {
            echo "Username already exists!";
        } elseif ($this->emailExists()) {
            echo "Email already exists!";
        } elseif (!$this->isValidEmail()) {
            echo "Please enter a valid email address!";
        } elseif (!$this->isValidPassword()) {
            echo "Please enter a valid password! The password must have a minimum length of 8 
                and contain at least an uppercase letter, a lowercase letter, and a digit.";
        } elseif (!$this->passwordCorrespondence()){
            echo "Passwords do not match!";
        }
        else {
            $this->registerUser();
            echo "Registration Successful!";
            $result = $this->getUserByEmail();
            $row = $result->fetch_assoc();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $this->email;
            $_SESSION['username'] = $this->username;
            $_SESSION['first_name'] = $this->firstName;
            $_SESSION['last_name'] = $this->lastName;
            $_SESSION['message'] = "Login successful!";
            header("Location: ../views/dashboard.php");
            exit();
        }
    }

    private function usernameExists(){
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }

    private function emailExists(){
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }

    private function isValidEmail(){
        return filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    private function passwordCorrespondence(){
        return ($this->password == $this->repeatedPassword);
    }

    private function isValidPassword(){
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
        return preg_match($pattern, $this->password);
    }

    private function registerUser(){
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (first_name, last_name, email, username, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $this->firstName, $this->lastName, $this->email, $this->username, $hashedPassword);
    
        if ($stmt->execute()) {
            echo "Registration Successful!";
        } else {
            echo "Error: " . $sql . "<br>" . $this->conn->error;
        }
    
        $stmt->close();    
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $repeatedPassword = $_POST['repeated_password'];

    $userRegistration = new UserRegistration($db);
    $userRegistration->signUp($firstName, $lastName, $email, $username, $password, $repeatedPassword);
}
?>
