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

$date = new Date();
$controller = new LoginController();

$basePage = new HTMLview();
$basePage->presentPage($controller->doControl());

/*
$dateAndTime = $date->getDateTime(true);

$basePage = new HTMLview();

$basePage->presentPage("Test + " . $dateAndTime);
*/