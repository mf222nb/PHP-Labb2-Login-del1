<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 16:56
 */
include_once("../View/View.php");
include_once("../Model/Model.php");

class LoginController{
    private $view;
    private $model;

    public function __construct(){
        $this->model = new Model;
        $this->view = new View($this->model);
    }

    public function doControl(){

        if($this->view->ifPersonUsedLogin()){
            //Om personen har tryckt på loginknappen

            $this->model->doLogin();
            //Försök att logga in...

        }

    }


}