<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 15:41
 */
require_once("Model/Date.php");
require_once("Model/UserModel.php");
require_once("Controller/LoginController.php");
require_once("Controller/RegisterController.php");
require_once("HTMLview.php");
require_once("View/View.php");

session_start();

$model = new UserModel();
//enligt mvc, skapa logincontroller (vår kontroller)
$controller = new LoginController();
$registerController = new RegisterController();
//skapa också vår htmlgrund, som hämtar data att fylla bodyn med från vår kontroller...
$basePage = new HTMLview();
$view = new view($model);

if($view->register()=== true){
    $basePage->presentPage($registerController->doControl());
}
else{
    $basePage->presentPage($controller->doControl());
}


