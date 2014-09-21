<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 17:09
 */
require_once("Model/Date.php");
include_once("Model/UserModel.php");
require_once("Posted.php");
include_once("CookieJar.php");

class view {
    private $model;
    public $CookieJar;

    public function __construct($model){
        $this->CookieJar = new CookieJar();
        $this->model = $model;
    }

    public function getClientidentifier($loginTroughCookies = false, $withoutUserName = false){
        //returnerar det aktiva användarnamnet...
        $arrayWithIdentifiers = array();
        $arrayWithIdentifiers[UserModel::$clientIp] = $_SERVER["REMOTE_ADDR"];// 0: Clients IP
        $arrayWithIdentifiers[UserModel::$clientBrowser] = $_SERVER["HTTP_USER_AGENT"];// 1: Clients browserdetails

        if($withoutUserName){
            //om ej användarnamn behövs så returnerar vi här
            return $arrayWithIdentifiers;
        }

        if($loginTroughCookies){
            //om $loginTroughCookies är true så ska användarnamnet i kakan returneras...
            $arrayWithIdentifiers[UserModel::$clientOnline] = $this->CookieJar->getUserOrPasswordFromCookie(true); // 2: username
            return $arrayWithIdentifiers;

        }else{
            //om kakor inte används, så har användaren loggat in och då hämtas namnet
            //via Post...
            $arrayWithIdentifiers[UserModel::$clientOnline] = $_POST["name"]; // 2: username
            return $arrayWithIdentifiers;
        }

    }

    public function loginTroughCookies(){
        $userName = $this->CookieJar->getUserOrPasswordFromCookie(true);
        $userPass = $this->CookieJar->getUserOrPasswordFromCookie(false);

        $shouldBeTrue = $this->model->tryLogin($userName, $userPass, true);

        //kollar så att kakan är giltig...
        $cookieIsLegal = $this->CookieJar->isCookieLegal($userName);

        //Kollar om clienten är samma... (för sessionstölder...)


        if($shouldBeTrue && $cookieIsLegal){
            //header("Location: " . $_SERVER["PHP_SELF"] . "?loggedin" . "&logintroughcookies");
            $_GET["loggedin"] = "";
            $_GET["logintroughcookies"] = "";
            return true;
        }else{
            $this->CookieJar->clearUserForRememberMe();
            $this->CookieJar->save("<p>Felaktig information i cookie</p></ b>");

            return false;
        }

    }

    public function userIsOnlineView(){

        if(isset($_GET["loggedin"])){
            //om $_GET["loggedin"] finns så är det första gången sidan laddas
            //Då ska vi presentera ett meddelande.. här skapas meddelandet..
            $_GET["loggedin"] = null; //undesettar denna...

            $welcomeString = "Inloggning lyckades";

            //var_dump($this);
            //die();

            if(isset($_GET["rememberme"])){
               //Om rememberMe finns, så ska vi lägga till en ytterligare sak i välkommstexten...
                $welcomeString .= " och vi kommer ihåg dig nästa gång";
                $_GET["rememberme"] = null; // unsettar denna

            }
            if(isset($_GET["logintroughcookies"])){
                $welcomeString .= " via cookies";
                $_GET["logintroughcookies"] = null; //unsettar denna...
            }

            $this->CookieJar->save($welcomeString);

            //när meddelandet är satt ska sidan laddas om utan "loggedin"...
            return $this->userIsOnlineView();
            //header("Location: " . $_SERVER["PHP_SELF"]);

        }else{

            if($this->hasUserdemandLogout()){
                //Om en användare har tryckt på logga ut så ska vi tömma
                $this->model->logoutUser();

                //Vi vill också tömma eventuella kakor...
                if($this->doesCookiesExist()){
                    $this->CookieJar->clearUserForRememberMe();
                }

                $this->CookieJar->save("Du har nu loggat ut!");
                //vi laddar om sidan, och har förberett ett meddelande till clienten
                header("Location: " . $_SERVER["PHP_SELF"]);
            }else{
                $message = $this->CookieJar->load();
                $viewToReturn = "
                    $message
                    <p>Du är inloggad!</p>
                    <a href='?logout'>Logga ut</a>
                    ";
                //var_dump($message);
                //die();
                return $viewToReturn;
            }

        }

    }

    public function hasUserdemandLogout(){
        if(isset($_GET["logout"])){
           return true;
        }
        else{
            return false;
        }
    }

    public function ifPersonUsedLogin(){
        if(isset($_POST["loginButton"])){

            return true;
        }
        return false;
    }

    public function doesCookiesExist(){

        if(@isset($_COOKIE["CookieUserName"]) && @$_COOKIE["CookieUserPass"]){
            return true;
        }
        return false;

    }


    public function ifPersonTriedToLogin(){
        //Vi testar om det angivna uppgifterna stämmer
        $hashedPassIfSucsess = $this->model->tryLogin(@$_POST["name"], @$_POST["password"]);

        if($hashedPassIfSucsess != false){
            // $hashedPassIfSucsess innehåller antingen det hashade lösenordet (om lyckad inloggning)
            // eller false, om lösenordet ej stämde...

            $forRememberMe = "";

            //Här kollar vi också om "rememberMe" är ikryssad.
            if(@isset($_POST["rememberme"]) && $_POST["rememberme"] == "on"){

                //Om den är det så ska vi spara undan lösen+användarnamn i kakor
                //som ska återanvändas nästa gång sidan besöks...
                $this->CookieJar->saveUserForRememberMe($_POST["name"],$hashedPassIfSucsess);
                //$forRememberMe = "&rememberme";
                $this->CookieJar = $this->CookieJar->setRememberMeToTrue();

                $_GET["rememberme"] = "";
            }

            //Vi lägger till GET så vi kan se när man precis loggat in...
            //var_dump($_SERVER);
            //header("Location: " . $_SERVER["PHP_SELF"] . "?loggedin" . $forRememberMe);
            $_GET["loggedin"] = "";

            return true;
        }else{
            return false;
        }
    }

    public function presentLoginForm(){

        //hämtar ut datum
        $date = new Date();
        $date = $date->getDateTime(true);

        //kollar om vi ska varna för tomt lösen/användarnamn:
        //För användarnamnet
        //var_dump($_POST);
        if(@trim($_POST["name"]) == "" && @isset($_POST["name"])){
            $message = "<p>Användarnamn saknas</p></ b>";
            $currentUserName = "";
        }else{
            $message = "";

            if(@$_POST["name"] != ""){
                //hämtar ut användarnamnet...
                $currentUserName = "value=" . @$_POST["name"];
            }else{
                //Om användarnamnet inte är "" och inte heller när den är trimmad fast ändå satt
                //Då sätter vi currentUserName till tomsträng ""...
                $currentUserName = "";
            }

        }


        //För lösenordet
        if(@trim($_POST["password"]) == "" && @isset($_POST["password"]) ){
            $message2 = "<p>Lösenord saknas</p></ b>";

        }else{
            $message2 ="";
        }

        //om inget meddelande skickas och användarnamnen är satta så är det fel på lösenordet/användarnamnet
        if( $message == "" &&
            $message2 == "" &&
            @isset($_POST["password"]) &&
            @isset($_POST["name"])){
            $message2 = "";
            $message = "<p>Felaktigt användarnamn och/eller lösenord</p></ b>";
        }

        //Om det finns något att hämta i vår CookieMessage-kaka så ska den presenteras
        $message3 = $this->CookieJar->load();

        //Htmln som ska åka ut på klienten
        $ToClient ="
                    <h3>Ej inloggad</h3>

                    <form  method='post'>
                        <fieldset>
                        $message
                        $message2
                        $message3
                            <legend>Login - Skriv in användarnamn och lösenord</legend>
                            <label for='name'>Namn</label>
                            <input type='text' id='name' name='name' $currentUserName >
                            <label for='pass'>Lösenord</label>
                            <input type='password' id='pass' name='password'>

                        </fieldset>
                        <input type='submit' value='Logga in' name='loginButton' >
                        <label for='rememberme'>Håll mig inloggad.</label>
                        <input type='checkbox' name='rememberme' id='rememberme'>
                    </form>

                    <p>$date</p>
                    ";




        return $ToClient;

    }


}