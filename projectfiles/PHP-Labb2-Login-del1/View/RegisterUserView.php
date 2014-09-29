<?php
/**
 * Created by PhpStorm.
 * User: Martin
 * Date: 2014-09-25
 * Time: 15:06
 */

require_once("Model/Date.php");

class RegisterUserView {
    private $username;
    private $password;
    private $repeatPass;
    private $message;
    private $message2;

    public function registerUserView(){
        $date = new Date();
        $date = $date->getDateTime(true);

        $ret = "
                <a href='?'>Tillbaka</a>
                <h3>Ej inloggad, Registrerar användare</h3>
                <form method='POST'>
                    <fieldset>
                    <p>$this->message</p>
                    <p>$this->message2</p>
                    <legend>Registrera ny användare - Skriv in användarnamn och lösenord</legend>
                            <label for='usernamename'>Namn</label>
                            <input type='text' id='username' name='username' value='$this->username'>
                            <label for='pass'>Lösenord</label>
                            <input type='password' id='pass' name='password'>
                            <label for='repeatpass'>Bekräfta lösenord</label>
                            <input type='password' id='repeatpass' name='repeatpass'>
                    </fieldset>
                    <input type='submit' value='Registrera' name='submit'>
                </form>
                <p>$date</p>
                ";

        return $ret;
    }

    public function getRegisterInformation(){
        if(isset($_POST["username"])){
            $this->username = $_POST["username"];
        }

        if(isset($_POST["password"])){
            $this->password = $_POST["password"];
        }

        if(isset($_POST["repeatpass"])){
            $this->repeatPass = $_POST["repeatpass"];
        }
    }

    public function didUserPressRegister(){
        if(isset($_POST["submit"])){
            return true;
        }
        return false;
    }

    public function getUsername(){
        return $this->username;
    }

    public function getPassword(){
        return $this->password;
    }

    public function getRepeatPass(){
        return $this->repeatPass;
    }

    public function usernameToShortMessage(){
        $this->message = "Användarnamnet har för få tecken. Minst 3 tecken";
    }

    public function passwordToShortMessage(){
        $this->message2 = "Lösenorden har för få tecken. Minst 6 tecken";
    }

    public function passwordDontMatchMessage(){
        $this->message2 = "Lösenorden matchar inte.";
    }

    public function usernameAlreadyExistMessage(){
        $this->message = "Användarnamnet är redan upptaget";
    }

    public function usernameContainInvalidCharacterMessage($e){
        $this->username = $e;
        $this->message = "Användarnamnet innehåller ogiltiga tecken";
    }
}