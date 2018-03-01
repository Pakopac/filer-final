<?php

require_once('Cool/BaseController.php');
require_once ('Model/RegisterManager.php');
require_once ('Model/LoginManager.php');

class MainController extends BaseController
{
    public function homeAction()
    {
        return $this->render('home.html.twig');
    }
    public function loginAction()
    {
        if(!empty($_POST['loginPseudo']) && !empty($_POST['loginPassword'])) {
            $pseudo = htmlentities($_POST['loginPseudo']);
            $password = $_POST['loginPassword'];

            $LoginManager = new LoginManager();
            $LoginManager -> loginUser($pseudo, $password);
        }
            return $this->render('login.html.twig');
    }
    public function registerAction()
    {
        if(!empty($_POST['firstname']) && !empty($_POST['lastname'])
            && !empty($_POST['pseudo']) && !empty($_POST['email'])
            && !empty($_POST['password'])){
            $firstname = htmlentities($_POST['firstname']);
            $lastname = htmlentities($_POST['lastname']);
            $pseudo = htmlentities($_POST['pseudo']);
            $email = htmlentities($_POST['email']);
            $password = $_POST['password'];

            $RegisterManager = new RegisterManager();
            $RegisterManager -> registerUser($firstname, $lastname, $pseudo, $email,$password);
        }
        return $this->render('register.html.twig');
    }
}
