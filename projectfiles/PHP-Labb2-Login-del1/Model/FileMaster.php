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
    //returnerar användarnamn eller lösenord
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
    //funktion som lägger in en tidstämpel på en användare.
    //om tidstämpeln är yngre än kakan som är satt till användaren. Och tidstämpen har gått ut, då är kakan ogiltig
        $TimeStamp = time() + $ammoutOfTime;

        //Lägg till tidstämpeln och användarnamnet i filen..
        if($this->userDoesAlreadyExist(self::$TimestampFile, $userName) != false){
        //Om den är något annat än falsk så har den retuernat en tidsstämpel
        //returnerar bara tidstämplar från användare som har = annars finns inget att ta bort

            //om timestamp finns så ska den bort.
            $this->removeLineFromFile($userName, self::$TimestampFile);
        }
        //Lägger till den nya timestampen...
        $pointer = fopen(self::$TimestampFile, 'a');
        fwrite($pointer,$userName . ":" . $TimeStamp . "\n");

        return $TimeStamp; //returnerar den...

    }

    function removeLineFromFile($whosLine, $fileToRemoveLineFrom){
    //tar bort en rad i en fil, baserat på vem som äger raden och vilken fil den ska leta i
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
    //denna funktion utnyttjar "userDoesAlreadyExist" och returnerar timestamp
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


}