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
    private $message;

    public function __construct($model){
        $this->CookieJar = new CookieJar();
        $this->model = $model;
    }

    public function getClientidentifier($loginTroughCookies = false, $withoutUserName = false){ //parametrarna är som standard false...
    //returnerar det aktiva användarnamnet, ip och webbläsarinfo som identifierare...
        $arrayWithIdentifiers = array();
        $arrayWithIdentifiers[UserModel::$clientIp] = $_SERVER["REMOTE_ADDR"];// Clients IP
        $arrayWithIdentifiers[UserModel::$clientBrowser] = $_SERVER["HTTP_USER_AGENT"];//  Clients browserdetails

        if($withoutUserName){
            //om ej användarnamn behövs så returnerar vi här
            return $arrayWithIdentifiers;
        }

        if($loginTroughCookies){
            //om $loginTroughCookies är true så ska användarnamnet i kakan returneras...
            $arrayWithIdentifiers[UserModel::$clientOnline] = $this->CookieJar->getUserOrPasswordFromCookie(true); // 2: username
            return $arrayWithIdentifiers;

        }else{
            //om kakor inte används, så har användaren loggat in och då hämtas namnet via Post...
            $arrayWithIdentifiers[UserModel::$clientOnline] = $_POST["name"]; // 2: username
            return $arrayWithIdentifiers;
        }

    }

    public function loginTroughCookies(){
        //hämtar ut användaruppgifterna i kakorna
        $userName = $this->CookieJar->getUserOrPasswordFromCookie(true);
        $userPass = $this->CookieJar->getUserOrPasswordFromCookie(false);

        //Testar om användarnamnet och lösenordet är giltiga
        $shouldBeTrue = $this->model->tryLogin($userName, $userPass, true);

        //kollar så att kakan är giltig... (tidsstämpeln ej för gammal..)
        $cookieIsLegal = $this->CookieJar->isCookieLegal($userName);

        //Sessionstölder kollas också, men det görs genom
        //LoginController>doControl>...>is userOnline (rad68)> här görs sessionstöldskollarna


        if($shouldBeTrue && $cookieIsLegal){
            //header("Location: " . $_SERVER["PHP_SELF"] . "?loggedin" . "&logintroughcookies");
            //header location förstörde, så jag satte GETs manuellt...
            $_GET["loggedin"] = "";
            $_GET["logintroughcookies"] = "";
            return true;
        }else{
            //om något inte stämmer så slängs datan och ett felmeddelande sparas för
            //att visas på loginskärmen...
            $this->CookieJar->clearUserForRememberMe();
            $this->CookieJar->save("<p>Felaktig information i cookie</p></ b>");

            return false;
        }

    }

    public function userIsOnlineView(){

        if(isset($_GET["loggedin"])){
            //om $_GET["loggedin"] finns så är det första gången sidan laddas
            //Då ska vi presentera ett meddelande.. här skapas meddelandet..
            $_GET["loggedin"] = null; //undesettar denna för att det inte påverkas nästa gång

            //Börjar på strängen som alltid kommer användas vid inloggning
            $welcomeString = "Inloggning lyckades";

            if(isset($_GET["rememberme"])){
               //Om rememberMe finns, så ska vi lägga till en ytterligare sak i välkommstexten...
                $welcomeString .= " och vi kommer ihåg dig nästa gång";
                $_GET["rememberme"] = null; // unsettar denna för att det inte påverkas nästa gång

            }
            if(isset($_GET["logintroughcookies"])){
                //samma som övre if-satsen..
                $welcomeString .= " via cookies";
                $_GET["logintroughcookies"] = null; //unsettar denna......
            }

            //sparar ner strängen för senare bruk
            $this->CookieJar->save($welcomeString);

            //när meddelandet är satt ska sidan laddas om utan "loggedin" (som vi nullade)...
            //då returneras detta och kör else-satsen istället...
            return $this->userIsOnlineView();
            //header("Location: " . $_SERVER["PHP_SELF"]); <-- detta är djävulen, jag lovar...

        }else{

            if($this->hasUserdemandLogout()){
                //Om en användare har tryckt på logga ut så ska vi tömma sessionsvariablarna som
                //identifierar inloggade användare...
                $this->model->logoutUser();

                //Vi vill också tömma eventuella kakor, om de finns...
                if($this->doesCookiesExist()){
                    $this->CookieJar->clearUserForRememberMe();
                }

                //sparar undan felmeddelande till senare
                $this->CookieJar->save("Du har nu loggat ut!");

                //vi laddar om sidan, och har förberett ett meddelande till clienten
                header("Location: " . $_SERVER["PHP_SELF"]);
                //^här är header(loc... ok då vi ändå inte behöver spara några variabler etc..
                //header(loc... börjar om från index.php och tömmer alla variabler som appen sparat...
            }else{
                //om ingen utloggning efterfrågas så presenteras eventuella meddelande och inloggadskärm.
                $message = $this->CookieJar->load();

                $viewToReturn = "
                    $message
                    <p>Du är inloggad!</p>
                    <a href='?logout'>Logga ut</a>
                    ";

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
        //hämtar ner inloggningsuppfterna och testar dessa med tryLogin
        $hashedPassIfSucsess = $this->model->tryLogin(@$_POST["name"], @$_POST["password"]);

        if($hashedPassIfSucsess != false){
        // $hashedPassIfSucsess innehåller antingen det hashade lösenordet (om lyckad inloggning)
        // eller false, om lösenordet ej stämde...

            //Här kollar vi också om "rememberMe" är ikryssad.
            if(@isset($_POST["rememberme"]) && $_POST["rememberme"] == "on"){

                //Om den är det så ska vi spara undan lösen+användarnamn i kakor
                //som ska återanvändas nästa gång sidan besöks...
                $this->CookieJar->saveUserForRememberMe($_POST["name"],$hashedPassIfSucsess);

                //anger en get som bekräftelse för att rememberme används, denna kollas av i userIsOnlineView
                $_GET["rememberme"] = "";
            }

            //Vi lägger till GET så vi kan se när man precis loggat in...
            $_GET["loggedin"] = "";

            //header("Location: " . $_SERVER["PHP_SELF"] . "?loggedin" . $forRememberMe);
                //^djävul!!! förstörde allt ett tag..

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
                    <a href='?Register'>Registrera ny användare</a>
                    <h3>Ej inloggad</h3>

                    <form  method='post' action='?login'>
                        <fieldset>
                        $message
                        $message2
                        $message3
                        $this->message
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

    public function register(){
        if(isset($_GET["Register"])){
            return true;
        }
        return false;
    }

    public function userAddedToDataBaseMessage(){
        $this->message = "<p>Registrering av ny användare lyckades</p>";
    }
}