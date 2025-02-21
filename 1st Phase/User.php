<?php
class User
{
    private $csvFile;
    private $email;
    private $username;
    private $first_name;
    private $last_name;


    public function __construct($csvFile = 'user_info.csv')
    {
        $this->csvFile = $csvFile;
        session_start();
    }

    public function login($email, $password){
        $this->email = $email;
        if ($this->loginPermission($password)) {
            $_SESSION['email'] = $this->email;
            $_SESSION['username'] = $this->username;
            $_SESSION['message'] = "Login successful!";
            $_SESSION['first_name'] = $this->email2Name();
            $_SESSION['last_name'] = $this->email2FamilyName();

            $this->redirect('dashboard.php');
        } else {
            echo "Wrong Username or Password!";
        }
    }

    private function loginPermission($enteredPassword){
        $file = fopen($this->csvFile, 'r');
        if ($file) {
            while (($row = fgetcsv($file)) !== FALSE) {
                if ($row[2] == $this->email && password_verify($enteredPassword, $row[4])) {
                    $this->username = $row[3];
                    fclose($file);
                    return True;
                }
            }
            fclose($file);
        }
        return False;
    }

    private function email2Name(){
        $file = fopen($this->csvFile, 'r');
        if ($file) {
            while (($row = fgetcsv($file)) !== FALSE) {
                if ($row[2] == $this->email) {
                    $this-> first_name = $row[0];
                    fclose($file);
                    return $this->first_name;
                }
            }
            fclose($file);
        }
    }

    private function email2FamilyName(){
        $file = fopen($this->csvFile, 'r');
        if ($file) {
            while (($row = fgetcsv($file)) !== FALSE) {
                if ($row[2] == $this->email) {
                    $this->last_name = $row[1];
                    fclose($file);
                    return $this->last_name;
                }
            }
            fclose($file);
        }
    }

    public function redirect($url, $statusCode = 303){
        header('Location: ' . $url, true, $statusCode);
        die();
    }


}