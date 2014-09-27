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
require_once("./Model/User.php");

class RegisterController {
    private $registerUserView;
    private $userModel;

    public function __construct(){
        $this->registerUserView = new RegisterUserView();
        $this->userModel = new UserModel();
    }

    public function doControl(){
        if($this->registerUserView->didUserPressRegister()){
            $this->registerUserView->getRegisterInformation();
            $username = $this->registerUserView->getUsername();
            $password = $this->registerUserView->getPassword();
            $repeatPass = $this->registerUserView->getRepeatPass();
            try{
                if($this->userModel->registerAuthentication($password, $repeatPass, $username)){
                    $cryptPass = $this->userModel->cryptPass($password);
                    $user = new User($username, $cryptPass);
                    $this->userModel->addUser($user);
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
        }
        $ret = $this->registerUserView->registerUserView();

        return $ret;
    }
}