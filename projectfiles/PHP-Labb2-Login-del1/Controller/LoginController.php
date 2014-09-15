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
    private $LoginModel;

    public function __construct(){
        $this->LoginModel = new UserModel();
        $this->view = new View($this->LoginModel);
    }

    public function doControl(){

        if($this->view->ifPersonUsedLogin()){
            //Om personen har tryckt på loginknappen

            $this->model->doLogin();
            //Försök att logga in...

        }else{
            return $this->view->presentLoginForm();
        }

    }


}