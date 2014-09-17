<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-16
 * Time: 18:40
 */
class CookieJar {
    //Class "tagen" från lektionsexempel : https://github.com/dntoll/1dv408-HT14/blob/master/Like/src/CookieStorage.php
    private static $cookieMessage = "CookieMessage";
    private static $cookieUserName = "CookieUserName";
    private static $cookieUserPass = "CookieUserPass";
    private $timeStampForCookies;
    private $rememberMeIsUsed;


    public function __construct(){
        $this->rememberMeIsUsed = false;
    }

    public function save($stringToSave){

        setcookie(self::$cookieMessage, $stringToSave, -1);
        $_COOKIE["CookieMessage"] = $stringToSave; //säkerhetsåtgärd... (tips från skolan..)
        //Sparar kakan i cookiearrayens nyckel "CookieMessage"
        //Värdet är värdet av $stringToSave
        //-1 = kakan försvinner när sessionen är klar.

    }

    public function load(){

        if(isset($_COOKIE[self::$cookieMessage])){
            //om det finns något i kakan så ska det returneras
            $returnThis = $_COOKIE[self::$cookieMessage];
        }else{
            $returnThis = ""; //annars returnerar vi tomsträng...
        }

        //nu när vi laddat kakan så vill vi se till att platsen är ledig
        //så vi slänger kakans värde, genom att sätta den till "".
        setcookie(self::$cookieMessage,"", time() -1);
        //Genom att ange "time() -1" så säger vi att detta hände för en sekund sen

        return $returnThis;
    }

    public function isCookieLegal(){
        //kollar så att ingen har manipulerat kakans tidsstämpel...

        if(time() > $this->timeStampForCookies){
            return false;
        }else{
            return true;
        }

    }

    public function saveUserForRememberMe($userName, $userPass){

        $this->timeStampForCookies = time() + 25;

        setcookie(self::$cookieUserName, $userName, $this->timeStampForCookies);
        setcookie(self::$cookieUserPass, $userPass, $this->timeStampForCookies);


    }

    public function getUserOrPasswordFromCookie($trueForUser){

        if($trueForUser){
            return $_COOKIE["CookieUserName"];
        }else{
            return $_COOKIE["CookieUserPass"];
        }

    }

    public function clearUserForRememberMe(){
        setcookie(self::$cookieUserName, null, -1);
        setcookie(self::$cookieUserPass, null, -1);
    }

    public function setRememberMeToTrue(){
        $this->rememberMeIsUsed = true;
        return $this;
    }

    public function isRememberMeUsed(){
        return $this->rememberMeIsUsed;
    }

}