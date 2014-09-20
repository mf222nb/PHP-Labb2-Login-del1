<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-20
 * Time: 21:50
 */

class FileMaster {
    //hjälper till med att skriva till filer...
    static $usersFile = "Model/users.txt";
    static $usersPassFile = "Model/usersPass.txt";
    static $TimestampFile = "Model/usersCookieTimestamp.txt";

    function getUserOrPasswordList($choice){
        $listToReturn = "";

        switch($choice){
            case "password":
                $listToReturn = file(self::$usersPassFile);
                break;
            case "username":
                $listToReturn = file(self::$usersFile);
                break;
            default:
                return false;
                break;
        }
        return $listToReturn;
    }

    function setAndGetTimestamp($userName, $ammoutOfTime = 50){

        $TimeStamp = time() + $ammoutOfTime;
        //Lägg till tidstämpeln och användarnamnet i filen..

        //skapar en pointer för fwrite med fopen...s

        if($this->userDoesNotAlreadyExist(self::$TimestampFile, $userName)){
            //om timestamp finns så ska den bort.
        }
        //Skriver ny timestamp
        $pointer = fopen(self::$TimestampFile, 'a');
        fwrite($pointer,$userName . ":" . $TimeStamp . "\n");

        return $TimeStamp;

    }

    function removeLineFromFile($whosLine, $fileToRemoveLineFrom){

        

    }

    function userDoesNotAlreadyExist( $fileToCheck, $userToCheck){
        //tar reda på om en användare redan finns på listan..

        $listToCheck = file($fileToCheck); //angivna listan görs om till array..

        $myRegEx = '/^'.$userToCheck.':.*/'; //regulärt uttryck för att hitta användarens kod

        for($i=0; $i<count($listToCheck);$i++){ //försöker hitta anvädnare

            if(preg_match($myRegEx, trim($listToCheck[$i])) == 1){
                return false; //false = user does exist

            }

        }
        return true;

    }

    function loadTimeStampfromUser($userName){
        $timeStamp = $this->findUsersCode($userName);
    }

    function findUsersCode($userName, $file){

        $myRegEx = '/^'.$userName.':.*/'; //regulärt uttryck för att hitta användaren
        //preg_match($myRegEx,trim($passList[$j]);

    }



}