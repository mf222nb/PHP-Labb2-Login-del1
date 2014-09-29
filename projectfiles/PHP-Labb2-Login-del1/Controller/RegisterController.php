<?php
/**
 * Created by PhpStorm.
 * User: Martin
 * Date: 2014-09-25
 * Time: 16:22
 */
require_once("./View/RegisterUserView.php");
require_once("./Model/UserModel.php");
require_once("./Helper/UsernameToShortException.php");
require_once("./Helper/PasswordToShortException.php");
require_once("./Helper/PasswordDontMatchException.php");
require_once("./Helper/UsernameContainsInvalidCharactersException.php");
require_once("./Model/User.php");
require_once("./View/View.php");
require_once("./Controller/LoginController.php");

class RegisterController {
    private $registerUserView;
    private $userModel;
    private $view;
    private $loginController;

    public function __construct(){
        $this->registerUserView = new RegisterUserView();
        $this->userModel = new UserModel();
        $this->view = new view($this->userModel);
        $this->loginController = new LoginController();
    }

    public function doControl(){
        if($this->registerUserView->didUserPressRegister()){
            $this->registerUserView->getRegisterInformation();
            $username = $this->registerUserView->getUsername();
            $password = $this->registerUserView->getPassword();
            $repeatPass = $this->registerUserView->getRepeatPass();
            try{
                if($this->userModel->registerAuthentication($password, $repeatPass, $username)){
                    if($this->userModel->UserAlreadyExist($username)){
                        $this->registerUserView->usernameAlreadyExistMessage();
                    }
                    else{
                        $cryptPass = $this->userModel->cryptPass($password);
                        $user = new User($username, $cryptPass);
                        $this->userModel->addUser($user);
                        $this->view->userAddedToDataBaseMessage();
                        return $this->view->presentLoginForm();
                    }
                }
            }
            catch(UsernameToShortException $e){
                $this->registerUserView->usernameToShortMessage();
            }
            catch(PasswordToShortException $e){
                $this->registerUserView->passwordToShortMessage();
            }
            catch(PasswordDontMatchException $e){
                $this->registerUserView->passwordDontMatchMessage();
            }
            catch(UsernameContainsInvalidCharactersException $e){
                $this->registerUserView->usernameContainInvalidCharacterMessage($e->getMessage());
            }
        }
        $ret = $this->registerUserView->registerUserView();

        return $ret;
    }
}