<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 17:09
 */
require_once("Model/Date.php");
require_once("Posted.php");
include_once("CookieJar.php");

class view {
    private $model;
    public $CookieJar;

    public function __construct($model){
        $this->CookieJar = new CookieJar();
        $this->model = $model;
    }

    public function getClientidentifier($loginTroughCookies = false){
        //returnerar det aktiva användarnamnet...
        if($loginTroughCookies){
            //om $loginTroughCookies är true så ska användarnamnet i kakan returneras...
            return $this->CookieJar->getUserOrPasswordFromCookie(true);

        }else{
            //om kakor inte används, så har användaren loggat in och då hämtas namnet
            //via Post...
            return $_POST["name"];
        }

    }

    public function loginTroughCookies(){
        $shouldBeTrue = $this->model->tryLogin($this->CookieJar->getUserOrPasswordFromCookie(true), $this->CookieJar->getUserOrPasswordFromCookie(false), true);

        //kollar så att kakan är giltig...
        $cookieIsLegal = $this->CookieJar->isCookieLegal();

        if($shouldBeTrue && $cookieIsLegal){
            header("Location: " . $_SERVER["PHP_SELF"] . "?loggedin" . "&logintroughcookies");
            return true;
        }else{
            $this->CookieJar->clearUserForRememberMe();
            $this->CookieJar->save("Felaktig information i cookie");

            return false;
        }

    }

    public function userIsOnlineView(){

        if(isset($_GET["loggedin"])){
            //om $_GET["loggedin"] finns så är det första gången sidan laddas
            //Då ska vi presentera ett meddelande.. här skapas meddelandet..

            $welcomeString = "Inloggning lyckades";

            //var_dump($this);
            //die();

            if(isset($_GET["rememberme"])){
               //Om rememberMe finns, så ska vi lägga till en ytterligare sak i välkommstexten...
                $welcomeString .= " och vi kommer ihåg dig nästa gång";

            }
            if(isset($_GET["logintroughcookies"])){
                $welcomeString .= " via cookies";
            }

            $this->CookieJar->save($welcomeString);

            //när meddelandet är satt ska sidan laddas om utan "loggedin"...
            header("Location: " . $_SERVER["PHP_SELF"]);

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
            if(@isset($_POST["rememberMe"]) && $_POST["rememberMe"] == "on"){
                //Om den är det så ska vi spara undan lösen+användarnamn i kakor
                //som ska återanvändas nästa gång sidan besöks...
                $this->CookieJar->saveUserForRememberMe($_POST["name"],$hashedPassIfSucsess);
                $forRememberMe = "&rememberme";
                //$this->CookieJar = $this->CookieJar->setRememberMeToTrue();
            }

            //Vi lägger till GET så vi kan se när man precis loggat in...
            header("Location: " . $_SERVER["PHP_SELF"] . "?loggedin" . $forRememberMe);

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
            $message = "<p>Användarnamn saknas</p>";
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
            $message2 = "<p>Lösenord saknas</p>";

        }else{
            $message2 ="";
        }

        //om inget meddelande skickas och användarnamnen är satta så är det fel på lösenordet/användarnamnet
        if( $message == "" &&
            $message2 == "" &&
            @isset($_POST["password"]) &&
            @isset($_POST["name"])){
            $message2 = "";

            $message = "Felaktigt användarnamn och/eller lösenord";
        }

        //Om det finns något att hämta i vår CookieMessage-kaka så ska den presenteras
        $message3 = $this->CookieJar->load();

        //Htmln som ska åka ut på klienten
        $ToClient ="
                    <h3>Ej inloggad</h3>
                    $message
                    $message2
                    $message3
                    <form  method='post'>
                        <fieldset>
                            <legend>Login - Skriv in användarnamn och lösenord</legend>
                            <label for='name'>Namn</label>
                            <input type='text' id='name' name='name' $currentUserName >
                            <label for='pass'>Lösenord</label>
                            <input type='password' id='pass' name='password'>

                        </fieldset>
                        <input type='submit' value='Logga in' name='loginButton' >
                        <label for='rememberMe'>Håll mig inloggad</label>
                        <input type='checkbox' name='rememberMe'>
                    </form>

                    <p>$date</p>
                    ";




        return $ToClient;

    }


}