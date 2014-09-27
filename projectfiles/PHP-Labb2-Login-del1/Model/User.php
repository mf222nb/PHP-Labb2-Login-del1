<?php
/**
 * Created by PhpStorm.
 * User: Martin
 * Date: 2014-09-26
 * Time: 14:16
 */

class User {
    private $username;
    private $password;

    public function __construct($username, $password){
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername(){
        return $this->username;
    }

    public function getPassword(){
        return $this->password;
    }
}