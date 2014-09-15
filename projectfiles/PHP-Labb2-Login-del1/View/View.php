<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 17:09
 */
require_once("Model/Date.php");
require_once("Posted.php");
class view {
    private $model;

    public function __construct($model){

        $this->model = $model;

    }

    public function ifPersonUsedLogin(){
        //användaren har tryckt på login knappen.

        //Först ska vi kolla om det finns en anvnädare med det angivna användarnamnet
        //1. hämta ner arrayen med användarnamn
        $users = file('View/users.txt');
        var_dump($users);

        for($i = 0; $i < count($users); $i++){

            if($users[$i] === @$_POST["name"]){
                //Om det finns så ska vi kolla om lösenordet matchar användarens användarnamn
                $userNameToMatchPassword = $users[$i];
                //Hämtar ner (det krypterade) lösenorden
                $passList = file("View/usersPass.txt");
                $myRegEx = '/^'.$userNameToMatchPassword.':.*/'; //regulärt uttryck för att hitta användarens lösenord

                for($j = 0; $j < count($passList); $j++ ){
                    $passList[$j];
                    //var_dump($myRegEx, $passList[$j]);
                    //var_dump(preg_match($myRegEx,$passList[$j]) == 1);
                    if(preg_match($myRegEx,$passList[$j])== 1){
                        //Om reg. matchar användarnamnet så har vi hittat lösenordet

                        //Tar bort den delen som identiferar vems lösenord det är (så bara lösenordet är kvar..)
                        $onlyPass = str_replace($userNameToMatchPassword.":", "" , $passList[$j]);

                        //Kollar om lösenordet matchar det inmatade lösenordet OBS här ska krypterings göras..
                        //TODO: Kryptera denna data...
                        if($onlyPass === $_POST["password"]){
                            return true;

                        }

                    }

                }




            }
        }




        return false;

    }

    public function presentLoginForm(){
        //hämtar ut datum
        $date = new Date();
        $date = $date->getDateTime(true);

        //kollar om vi ska varna för tomt lösen/användarnamn:
        //För användarnamnet
        var_dump($_POST);
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



        //Htmln som ska åka ut på klienten
        $ToClient ="
                    <h3>Ej inloggad</h3>
                    $message
                    $message2
                    <form action='' method='post'>
                        <fieldset>
                            <legend>Login - Skriv in användarnamn och lösenord</legend>
                            <label for='name'>Namn</label>
                            <input type='text' id='name' name='name' value=$currentUserName>
                            <label for='pass'>Lösenord</label>
                            <input type='text' id='pass' name='password'>

                        </fieldset>
                        <input type='submit' value='Logga in' >
                    </form>

                    <p>$date</p>
                    ";



        return $ToClient;

    }


}