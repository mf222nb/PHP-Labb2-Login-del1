<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-20
 * Time: 21:18
 */
class SessionMaster {

    function __construct(){

    }

    public function save($sessionName, $Value){
        $_SESSION[$sessionName] = $Value;
    }
    public function cleanSpecificSession($specificSession){
        $_SESSION[$specificSession] = null;
    }

    public function LoadSessionValue($specificSession){

        return $_SESSION[$specificSession];
    }




}