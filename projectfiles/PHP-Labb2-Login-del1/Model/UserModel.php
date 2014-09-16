<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 17:25
 */
Class UserModel {

    private $name;
    private $password;
    private $userIsOnline; //online eller offline

    public function __construct(){

    }

    public function doLogin($clientID){
        //Användarnamnet+lösenordet är rätt, nu kan vi räknas som inloggade

        $_SESSION["ClientOnline"] = $clientID;

    }

    public function isUserOnline(){
        if(isset($_SESSION["ClientOnline"])){
            return true;
        }

        return false;
    }

    public function logoutUser(){
        $_SESSION["ClientOnline"] = null;
    }

    public function tryLogin($username, $password){
        //användaren har tryckt på login knappen.

        //Först ska vi kolla om det finns en anvnädare med det angivna användarnamnet
        //1. hämta ner arrayen med användarnamn
        $users = file('View/users.txt');
        //var_dump($users);

        for($i = 0; $i < count($users); $i++){

            if($users[$i] === $username){
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
                        if($onlyPass === $password){
                            return true;

                        }

                    }

                }




            }
        }
        return false;
    }

}