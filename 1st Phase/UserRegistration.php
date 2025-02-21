<?php
class UserRegistration
{
    private $csvFile;
    private $firstName;
    private $lastName;
    private $email;
    private $username;
    private $password;
    private $repeatedPassword;

    public function __construct($csvFile = 'user_info.csv')
    {
        $this->csvFile = $csvFile;
        session_start();
    }

    public function signUp($firstName, $lastName, $email, $username, $password, $repeatedPassword){
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->repeatedPassword = $repeatedPassword;
        $file = fopen($this->csvFile, 'a+');

        if ($this->usernameExists($file)) {
            echo "Username already exists!";
        } elseif ($this->emailExists($file)) {
            echo "Email already exists!";
        } elseif (!$this->isValidEmail()) {
            echo "Please enter a valid email address!";
        } elseif (!$this->isValidPassword()) {
            echo "Please enter a valid password! The password must have a minimum length of 8 
                and contain at least an uppercase letter, a lowercase letter, and a digit.";
        } elseif (!$this->password_correspondence()){
            echo "Passwords do not match!";
        }
        else {
            $this->registerUser($file);
            echo "Registration Successful!";
            $_SESSION['email'] = $this->email;
            $_SESSION['username'] = $this->username;
            $_SESSION['first_name'] = $this->firstName;
            $_SESSION['last_name'] = $this->lastName;
            $_SESSION['message'] = "Login successful!";
            header("Location: dashboard.php");
            exit();
        }

        fclose($file);
    }

    private function usernameExists($file){
        while (($row = fgetcsv($file)) !== FALSE) {
            if ($row[3] == $this->username)
                return True;
        }
        return False;
    }

    private function emailExists($file){
        rewind($file);
        while (($row = fgetcsv($file)) !== FALSE) {
            if ($row[2] == $this->email)
                return True;
        }
        return False;
    }

    private function isValidEmail(){
        return filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    private function password_correspondence(){
        return ($this->password == $this->repeatedPassword);
    }

    private function isValidPassword(){
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
        return preg_match($pattern, $this->password);
    }

    private function registerUser($file){
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        fputcsv($file, [$this->firstName, $this->lastName, $this->email, $this->username, $hashedPassword]);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $repeatedPassword = $_POST['repeated_password'];

    $userRegistration = new UserRegistration();
    $userRegistration->signUp($firstName, $lastName, $email, $username, $password, $repeatedPassword);
}
?>