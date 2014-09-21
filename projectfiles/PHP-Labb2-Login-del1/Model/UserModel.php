<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 17:25
 */
require_once("FileMaster.php");
Class UserModel {

    private $name;
    private $password;
    private $userIsOnline; //online eller offline
    private $fileMaster;
    static $clientOnline = "ClientOnline";
    static $clientIp = "ClientIp";
    static $clientBrowser = "ClientBrowser";

    public function __construct(){
        $this->fileMaster = new FileMaster();
    }

    public function isNOTSessionThief($clientIdentifier){

        if( $_SESSION[self::$clientIp] == $clientIdentifier[self::$clientIp]&&
            $_SESSION[self::$clientBrowser] == $clientIdentifier[self::$clientBrowser]){
            return true;
        }
        $this->logoutUser();
        return false;
    }

    public function doLogin($identifier){
        //Användarnamnet+lösenordet är rätt, nu kan vi räknas som inloggade

        $_SESSION[self::$clientOnline] = $identifier[self::$clientOnline];

        //Här ska vi även sätta spärrar som berättar vad anvädnare använde för ip eller webbläsare.
        $_SESSION[self::$clientIp] = $identifier[self::$clientIp];
        $_SESSION[self::$clientBrowser] = $identifier[self::$clientBrowser];

    }

    public function isUserOnline(){

        if(isset($_SESSION[self::$clientOnline])){
            return true;
        }

        return false;
    }

    public function logoutUser(){

        $_SESSION[self::$clientOnline] = null;
    }

    public function cryptPass($passwordToCrypt, $rounds = 9){
        //krypterar lösenordet med blowfish, har följt denna guide https://www.youtube.com/watch?v=wIRtl8CwgIc
        //returnerar det krypterade lösenordet
        //OBS: blowfish verkar kräva nyare version av php. version.5.2.12 verkar ej funka...

        $salt = "";

        //skapar en lång array med alla (typ) tecken från alfabetet + siffrorna 0-9
        $saltChars = array_merge(range("A","Z"), range("a","z"), range(0,9));

        for($i=0;$i < 22;$i++){ // for loop som ska utföras 22 gånger, Blowfish behöver 22 blandade tecken..
            $salt .= $saltChars[array_rand($saltChars)];
                // För varje "varv" så läggs en slumpad karaktär från arrayen in i strängen $salt (22blandade tecken)
        }

        //Nu ska vi kryptera och returnera det krypterade lösenordet!


        return crypt($passwordToCrypt, sprintf("$2y$%02d$", $rounds) . $salt);//
        // "$2y$%02d$" är den delen som gör att vi krypterar genom blowfish, endast "$2y$" är nödvändigt
        // det andra är "extra saker" som killen i videon sa var bra (hörde inte riktigt...)
    }

    public function checkIfPasswordIstrue($inputFromUser, $usersHashedPassword){
        //vi krypterar det inmatade lösenordet, testar om det är samma som användarens lösenord
        //om så är fallet så stämde lösenordet...

        $shouldBeSameAsHashed = crypt($inputFromUser, $usersHashedPassword);

        //var_dump($shouldBeSameAsHashed);
        //var_dump($usersHashedPassword);
        //var_dump($shouldBeSameAsHashed);
        //die(); //jag är tröttss

        if($shouldBeSameAsHashed == $usersHashedPassword){
            return true;
        }else{
            return false;
        }


    }
    /*
    public function tryLoginTroughCookies($username, $password){
        $users = file('View/users.txt');
        for($i=0;$i< count($users);$i++){
            //kollar om det anginva användarnamnet finns i
            if($$users[$i] == $username){

            }
        }
    }*/

    public function tryLogin($username, $password, $loginTroughCookies = false){
        //användaren har tryckt på login knappen.

        //Först ska vi kolla om det finns en anvnädare med det angivna användarnamnet
        //1. hämta ner arrayen med användarnamn
        $users = $this->fileMaster->getUserOrPasswordList("username");
        //var_dump($users);

        for($i = 0; $i < count($users); $i++){
            //var_dump(trim($users[$i]));
            //var_dump(trim($username));
            //die();
            if(trim($users[$i]) === $username){
                //Om det finns så ska vi kolla om lösenordet matchar användarens användarnamn
                $userNameToMatchPassword = trim($users[$i]);
                //Hämtar ner (det krypterade) lösenorden
                $passList = $this->fileMaster->getUserOrPasswordList("password");
                $myRegEx = '/^'.$userNameToMatchPassword.':.*/'; //regulärt uttryck för att hitta användarens lösenord

                for($j = 0; $j < count($passList); $j++ ){
                    $passList[$j]; //användarnamnet + hashat lösenordet...
                    //var_dump($myRegEx, $passList[$j]);
                    //var_dump(preg_match($myRegEx,$passList[$j]) == 1);
                    //var_dump($passList[$j]);

                    if(preg_match($myRegEx,trim($passList[$j]))== 1){
                        //Om reg. matchar användarnamnet så har vi hittat lösenordet

                        //Tar bort den delen som identiferar vems lösenord det är (så bara lösenordet är kvar..)
                        $onlyPass = str_replace($userNameToMatchPassword.":", "" , $passList[$j]);
                        $onlyPass = trim($onlyPass);

                        //Kollar om lösenordet matchar det inmatade lösenordet OBS här ska krypterings göras..
                        //TOD: Kryptera denna data... KLART

                        //var_dump($onlyPass);//$this->cryptPass($onlyPass)
                        //var_dump($this->checkIfPasswordIstrue($password,$onlyPass));
                        //die();

                        if($loginTroughCookies){

                            if($password == $onlyPass){
                                return true;
                            }else{
                                //om detta ej stämmer så är det något fel på kakan
                                //den är troligtvis manipulerad, vi ska ta bort allt då...
                                return false;
                            }

                        }else{
                            // om $loginTroughCookies inte används, så är det en vanlig inloggning

                            if($this->checkIfPasswordIstrue($password,$onlyPass)){//$password
                                return $onlyPass;   //vi returnerar det hashade lösenordet om
                                //det stämde, då kan vi lägga den i en kaka...

                            }
                        }



                    }
                }
            }
        }
        return false;
    }

}