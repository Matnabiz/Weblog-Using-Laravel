<?php 
define('BASE_ROOT_PATH', realpath(__DIR__ . '/../' . '/../'));
require_once BASE_PATH.'/config/config.php';

class UserLogin
{
    private $conn;
    private $entered_email;
    private $entered_password;
    private $username;
    private $first_name;
    private $last_name;


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


    public function login($entered_email, $entered_password){
        $this->entered_email = $entered_email;
        $this->entered_password = $entered_password;
    
        $result = $this->getUserByEmail();
    
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];
            
            if (password_verify($entered_password, $hashed_password)) {
                session_start();

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $this->entered_email;
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
    
        $stmt->close();
    }
    

}