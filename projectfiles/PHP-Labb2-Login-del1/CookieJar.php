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

    public function save($stringToSave){

        setcookie(self::$cookieMessage, $stringToSave, -1);
        //Sparar kakan i cookiearrayens nyckel "CookieMessage"
        //Värdet är värdet av $stringToSave
        //-1 = kakan försvinner när sessionen är klar. webbläsaren stängts
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

}