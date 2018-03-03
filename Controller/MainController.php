<?php
require_once('Cool/BaseController.php');
require_once ('Model/RegisterManager.php');
require_once ('Model/LoginManager.php');
var_dump(session_start());

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
            if(isset($_SESSION['pseudo'])) {
                $data = ['pseudo' => $_SESSION['pseudo']];
                return $this->render('home.html.twig', $data);
            }
        }
        return $this->render('login.html.twig');
    }
    public function registerAction()
    {
        if(!empty($_POST['firstname']) && !empty($_POST['lastname'])
            && !empty($_POST['pseudo']) && !empty($_POST['email'])
            && !empty($_POST['password']) && !empty($_POST['repeatPassword'])){
            $firstname = htmlentities($_POST['firstname']);
            $lastname = htmlentities($_POST['lastname']);
            $pseudo = htmlentities($_POST['pseudo']);
            $email = htmlentities($_POST['email']);
            $password = $_POST['password'];
            $repeatPassword = $_POST['repeatPassword'];

            $RegisterManager = new RegisterManager();
            $RegisterManager -> registerUser($firstname, $lastname, $pseudo, $email,$password, $repeatPassword);
            $data = ['pseudo' => $_SESSION['pseudo']];
            return $this->render('home.html.twig', $data);
        }
        return $this->render('register.html.twig');
    }
    public function UploadFilesAction()
    {
        return $this->render('uploadFiles.html.twig');
    }
    public function viewFilesAction()
    {
        return $this->render('viewFiles.html.twig');
    }
}
