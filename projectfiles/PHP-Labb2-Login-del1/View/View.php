<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 17:09
 */
require_once("Model/Date.php");
class view {
    private $model;

    public function __construct($model){

        $this->model = $model;

    }

    public function ifPersonUsedLogin(){

        return false;

    }

    public function presentLoginForm(){
        $date = new Date();
        $date = $date->getDateTime(true);
        $ToClient ="
                    <h3>Ej inloggad</h3>
                    <form>
                        <fieldset>
                            <legend>Login - Skriv in användarnamn och lösenord</legend>
                            <label for='name'>Namn</label>
                            <input type='text' id='name' name='Namn'>
                            <label for='pass'>Lösenord</label>
                            <input type='text' id='pass' name='Lösenord'>

                        </fieldset>
                    </form>
                    <input type='submit'>
                    <p>$date</p>

        ";

        return $ToClient;

    }


}