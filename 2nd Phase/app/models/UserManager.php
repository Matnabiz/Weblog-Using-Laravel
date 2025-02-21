<?php 
define('BASE_ROOT_PATH', realpath(__DIR__ . '/../' . '/../'));
require_once BASE_PATH.'/config/config.php';

class UserManager
{
    private $conn;
    private $enteredEmail;
    private $enteredPassword;
    private $repeatedPassword;
    private $username;
    private $firstName;
    private $lastName;



    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getidByUsername($username){
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        return $user_id;
    }

    public function getUserByEmail(){
        $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->enteredEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public function login($entered_email, $entered_password){
        $this->enteredEmail = $entered_email;
        $this->enteredPassword = $entered_password;
    
        $result = $this->getUserByEmail();
    
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];
            
            if (password_verify($entered_password, $hashed_password)) {
                session_start();

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $this->enteredEmail;
                $_SESSION['first_name'] = $row['first_name'];
                $_SESSION['last_name'] = $row['last_name'];
                
                header("Location: ../views/dashboard.php");
                exit();
            } else {
                echo "Invalid username or password.";
            }
        } else {
            echo "No user found with that email.";
        }
    
    }

    public function signUp($firstName, $lastName, $enteredEmail, $username, $enteredPassword, $repeatedPassword){
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->enteredEmail = $enteredEmail;
        $this->username = $username;
        $this->enteredPassword = $enteredPassword;
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
            $_SESSION['email'] = $this->enteredEmail;
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
        $stmt->bind_param("s", $this->entered_Email);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }

    private function isValidEmail(){
        return filter_var($this->enteredEmail, FILTER_VALIDATE_EMAIL);
    }

    private function passwordCorrespondence(){
        return ($this->enteredPassword == $this->repeatedPassword);
    }

    private function isValidPassword(){
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
        return preg_match($pattern, $this->enteredPassword);
    }

    private function registerUser(){
        $hashedPassword = password_hash($this->enteredPassword, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (first_name, last_name, email, username, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $this->firstName, $this->lastName, $this->enteredEmail, $this->username, $hashedPassword);
    
        if ($stmt->execute()) {
            echo "Registration Successful!";
        } else {
            echo "Error: " . $sql . "<br>" . $this->conn->error;
        }
    
        $stmt->close();    
    }

    
}


