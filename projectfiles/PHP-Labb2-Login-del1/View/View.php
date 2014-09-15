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

        return false;

    }

    public function presentLoginForm(){
        //hämtar ut datum
        $date = new Date();
        $date = $date->getDateTime(true);

        //kollar om vi ska varna för tomt lösen/användarnamn
        var_dump($_POST);
        if(@trim($_POST["name"]) == "" && @isset($_POST["name"])){
            $message = "<p>Användarnamn saknas</p>";
        }else{
            $message = "";
        }
        if(@trim($_POST["password"]) == "" && @isset($_POST["password"]) ){
            $message2 = "<p>Lösenord saknas</p>";
        }else{
            $message2 ="";
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
                            <input type='text' id='name' name='name'>
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