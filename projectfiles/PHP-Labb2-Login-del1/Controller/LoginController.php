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

    public function __construct(){
        $this->UserModel = new UserModel();
        $this->view = new View($this->UserModel);
    }

    public function doControl(){

        if($this->view->hasUserdemandLogout()){
            //Om en användare har tryckt på logga ut så ska vi tömma
            $this->UserModel->logoutUser();

            $message = "Du har nu loggat ut!";

            //vi laddar om sidan, och har förberett ett meddelande till clienten
            header("Location: " . $_SERVER["PHP_SELF"]);
        }

        if($this->view->ifPersonUsedLogin()){
            //Om personen har tryckt på loginknappen och blivit godkänd

            $clientID = $this->view->getClientidentifier();

            //Gör användare inloggad
            $this->UserModel->doLogin($clientID );

            //Vi skickar med ett meddelande som säger att inloggning lyckades
            return $this->view->userIsOnlineView("Inloggning lyckades");



        }else{
            //vi kollar även om personen redan är inloggad..
            if($this->UserModel->isUserOnline()){
                //I det fallet ska vi presentera utloggningssidan

                //här skickar vi med en tomsträng, vi behöver inte
                //berätta att inloggning lyckades här...
                return $this->view->userIsOnlineView("");
            }


            return $this->view->presentLoginForm();
        }

    }


}