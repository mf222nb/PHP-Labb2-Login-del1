<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 16:56
 */
include_once("View/View.php");
include_once("Model/UserModel.php");
include_once("CookieJar.php");

class LoginController{
    public $view;
    private $UserModel;

    public function __construct(){

        $this->UserModel = new UserModel();
        $this->view = new View($this->UserModel);
    }

    public function doControl(){

        if($this->view->doesCookiesExist() && $this->UserModel->isUserOnline() == false){
            //anropar vyn för att ta reda på om kakor är satta och ingen redan är inloggad... för inloggning

            if($this->view->loginTroughCookies()){
                //Om personen har  blivit godkänd

                $clientID = $this->view->getClientidentifier(true);

                //Gör användare inloggad
                $this->UserModel->doLogin($clientID);

                return $this->view->userIsOnlineView();
            }
        }

        if($this->view->ifPersonUsedLogin() && $this->UserModel->isUserOnline() == false ){
            //Om personen har tryckt på loginknappen
            $haveUserBeenAccepted = $this->view->ifPersonTriedToLogin();


            if($haveUserBeenAccepted){

                //Om personen har  blivit godkänd
                $clientID = $this->view->getClientidentifier();

                //Gör användare inloggad
                $this->UserModel->doLogin($clientID);

                $ret = $this->view->userIsOnlineView();


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


