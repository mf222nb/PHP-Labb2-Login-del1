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

        if($this->userDoesAlreadyExist(self::$TimestampFile, $userName) != false){
        //Om den är något annat än falsk så har den retuernat ett tidsstämpeln

            //om timestamp finns så ska den bort.
            $this->removeLineFromFile($userName, self::$TimestampFile);
        }
        //Skriver ny timestamp
        $pointer = fopen(self::$TimestampFile, 'a');
        fwrite($pointer,$userName . ":" . $TimeStamp . "\n");

        return $TimeStamp;

    }

    function removeLineFromFile($whosLine, $fileToRemoveLineFrom){

        $fileToRemoveLineFromPointer = file($fileToRemoveLineFrom);

        $myRegEx = '/^'.$whosLine.':.*/'; //regulärt uttryck för att hitta användarens kod
        $rowsInNewFile = array();

        foreach($fileToRemoveLineFromPointer as $line){
            if(preg_match($myRegEx, trim($line)) == 0){ //om den inte matchar, spara raden.
                $rowsInNewFile[] = $line; //alla rader som inte matchar ska sparas

            }
        }

        // nu Tömmer vi filen och fyller på den med innehållet från den nya arrayen...
        $fileToRemoveLineFromPointer = fopen($fileToRemoveLineFrom, 'w');
        foreach($rowsInNewFile as $line){
            fwrite($fileToRemoveLineFromPointer, $line); //ersätter det gamla raderna med de nya
        }

    }

    function returnTimestamp($userToCheck){
    //denna funktion utnyttjar "userDoesAlreadyExist" och returnerar
        return $this->userDoesAlreadyExist(self::$TimestampFile, $userToCheck);
    }

    function userDoesAlreadyExist( $fileToCheck, $userToCheck){
        //tar reda på om en användare redan finns på listan.. om den finns, returnera timestamp

        $listToCheck = file($fileToCheck); //angivna listan görs om till array..

        $myRegEx = '/^'.$userToCheck.':.*/'; //regulärt uttryck för att hitta användarens kod

        for($i=0; $i<count($listToCheck);$i++){ //försöker hitta anvädnare

            if(preg_match($myRegEx, trim($listToCheck[$i])) == 1){

                $timeStamp = str_replace($userToCheck.":","", trim($listToCheck[$i]));
                return $timeStamp; //false = user does exist (obs, ändrad. bara false om den inte finns, annars returnera tidsstämpeln

            }

        }
        return false;

    }

    function loadTimeStampfromUser($userName){
        $timeStamp = $this->findUsersCode($userName);
    }

    function findUsersCode($userName, $file){

        $myRegEx = '/^'.$userName.':.*/'; //regulärt uttryck för att hitta användaren
        //preg_match($myRegEx,trim($passList[$j]);

    }



}