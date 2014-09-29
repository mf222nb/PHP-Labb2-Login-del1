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
include_once("View/RegisterUserView.php");
include_once("RegisterController.php");

class LoginController{
    public $view;
    private $UserModel;
    private $registerUserView;
    public function __construct(){

        $this->UserModel = new UserModel();
        $this->view = new View($this->UserModel);
        $this->registerUserView = new RegisterUserView();
    }

    public function doControl(){
    //Funktionen tar reda på vad som ska göras, är användaren inloggad, har den kakor, ska den loggas in
    //och utför dessa...

        if($this->view->doesCookiesExist() && $this->UserModel->isUserOnline() == false){
        //anropar vyn för att ta reda på om kakor är satta och ingen redan är inloggad...
        //om det är så, så ska vi försöka logga in användaren genom kakor

            if($this->view->loginTroughCookies()){
            //Om personens kakor har  blivit godkända

                //hämtar identifierare för clienten (skicka med true innebär att
                //användarnamn ej skickas med.. den behövs ej för att verifiera...
                $clientID = $this->view->getClientidentifier(true);

                //Gör användare inloggad, sätter identifierare...
                $this->UserModel->doLogin($clientID);

                //returnerar vyn för inloggade...
                return $this->view->userIsOnlineView();
            }
        }

        if($this->view->ifPersonUsedLogin() && $this->UserModel->isUserOnline() == false ){
        //Om personen har tryckt på loginknappen och inte redan är inloggad

            //kolla om uppgifterna stämmer, (true/false)
            $haveUserBeenAccepted = $this->view->ifPersonTriedToLogin();


            if($haveUserBeenAccepted){
            //Om personen har  blivit godkänd

                $clientID = $this->view->getClientidentifier();

                //Gör användare inloggad, skicka med identifierare...
                $this->UserModel->doLogin($clientID);

                //returnerar vyn för inloggade
                $ret = $this->view->userIsOnlineView();

                return $ret;
            }

            //om uppgifterna ej stämde så är vi kvar på loginformuläret
            return $this->view->presentLoginForm();

        }else{
            //vi kollar även om personen redan är inloggad..


            if($this->UserModel->isUserOnline()){
            //I det fallet ska vi presentera den inloggade sidan

                // eftersom inloggade användare kommer in här så ska vi se till att det ej är sessionstjuvar.
                if($this->UserModel->isNOTSessionThief($this->view->getClientidentifier(true, true))){

                    //returnerar vyn för inloggade...
                    return $this->view->userIsOnlineView();
                }
            }

            //returnerar vy för logga in formuläret...
            $ret = $this->view->presentLoginForm();

            return $ret;
        }

    }
}


