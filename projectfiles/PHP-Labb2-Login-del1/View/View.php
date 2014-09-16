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
    private $CookieJar;

    public function __construct($model){
        $this->CookieJar = new CookieJar();
        $this->model = $model;

    }
    public function getClientidentifier(){
        //returnerar det aktiva användarnamnet...

        return $_POST["name"];

    }

    public function userIsOnlineView(){

        if(isset($_GET["loggedin"])){
            //om $_GET["loggedin"] finns så är det första gången sidan laddas
            //Då ska vi presentera ett meddelande.. här skapas meddelandet..
            $this->CookieJar->save("Inloggning lyckades");

            //när meddelandet är satt ska sidan laddas om utan "loggedin"...
            header("Location: " . $_SERVER["PHP_SELF"]);

        }else{
            if($this->hasUserdemandLogout()){
                //Om en användare har tryckt på logga ut så ska vi tömma
                $this->model->logoutUser();

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

    public function ifPersonTriedToLogin(){
        //Vi testar om det angivna uppgifterna stämmer
        if($this->model->tryLogin(@$_POST["name"], @$_POST["password"])){
            //Vi lägger till GET så vi kan se när man precis loggat in...

            header("Location: " . $_SERVER["PHP_SELF"] . "?loggedin");
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

            //hämtar ut användarnamnet...
            $currentUserName = @$_POST["name"];
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
                    <form action='' method='post'>
                        <fieldset>
                            <legend>Login - Skriv in användarnamn och lösenord</legend>
                            <label for='name'>Namn</label>
                            <input type='text' id='name' name='name' value=$currentUserName>
                            <label for='pass'>Lösenord</label>
                            <input type='text' id='pass' name='password'>

                        </fieldset>
                        <input type='submit' value='Logga in' name='loginButton' >
                    </form>

                    <p>$date</p>
                    ";



        return $ToClient;

    }


}