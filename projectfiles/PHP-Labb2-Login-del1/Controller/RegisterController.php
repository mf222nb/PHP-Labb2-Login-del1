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
require_once("./Helper/UsernameAndPasswordToShortException.php");
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
        /*Om användaren trycker på Register knappen så hämtar vi ut information om vad användaren skrev in i fälten
        och sedan kollar vi så att de uppfyller alla kraven så som längd etc, och om de gör det så tittar vi om
        användarnamnet är ledigt. Är användarnamnet ledigt så krypterar vi lösenordet och sedan lägger till det i
        databasen och man kommer tillbaka till loginviewn.

        Om något går fel så får användaren ett felmeddelande av något slag.*/
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
            catch(UsernameAndPasswordToShortException $e){
                $this->registerUserView->usernameAndPasswordToShortMessage();
            }
        }
        $ret = $this->registerUserView->registerUserView();

        return $ret;
    }
}