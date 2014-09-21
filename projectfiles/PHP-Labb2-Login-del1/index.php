<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 15:41
 */
require_once("Model/Date.php");
require_once("Controller/LoginController.php");
require_once("HTMLview.php");

session_start();

//enligt mvc, skapa logincontroller (vår kontroller)
$controller = new LoginController();

//skapa också vår htmlgrund, som hämtar data att fylla bodyn med från vår kontroller...
$basePage = new HTMLview();
$basePage->presentPage($controller->doControl());



