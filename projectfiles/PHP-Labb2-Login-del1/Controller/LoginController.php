<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 16:56
 */
include_once("View/View.php");
include_once("Model/UserModel.php");

class LoginController{
    private $view;
    private $UserModel;
    private $CookieJar;

    public function __construct(){
        $this->CookieJar = new CookieJar();
        $this->UserModel = new UserModel();
        $this->view = new View($this->UserModel);
    }

    public function doControl(){

        if($this->view->ifPersonUsedLogin()){
            //Om personen har tryckt på loginknappen

            if($this->view->ifPersonTriedToLogin()){
                //Om personen har  blivit godkänd
                $clientID = $this->view->getClientidentifier();

                //Gör användare inloggad
                $this->UserModel->doLogin($clientID);

                $ret = $this->view->userIsOnlineView();
                //header("Location: " . $_SERVER["PHP_SELF"]);
                return $ret;
            }

            return $this->view->presentLoginForm();

        }else{
            //vi kollar även om personen redan är inloggad..
            if($this->UserModel->isUserOnline()){
                //I det fallet ska vi presentera utloggningssidan

                //här skickar vi med en tomsträng, vi behöver inte
                //berätta att inloggning lyckades här...
                return $this->view->userIsOnlineView();
            }
            $ret = $this->view->presentLoginForm();

            return $ret;



        }

    }
}


