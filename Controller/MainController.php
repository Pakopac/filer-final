<?php
require_once('Cool/BaseController.php');
require_once('Model/RegisterManager.php');
require_once('Model/LoginManager.php');
require_once('Model/FilesManager.php');
session_start();
class MainController extends BaseController
{
    public function homeAction()
    {
        if(isset($_SESSION)) {
            $data = ['user' => $_SESSION];
            return $this->render('home.html.twig',$data);
        }
        return $this->render('home.html.twig');
    }
    public function loginAction()
    {
        if(!empty($_POST['loginPseudo']) && !empty($_POST['loginPassword'])) {
            $pseudo = htmlentities($_POST['loginPseudo']);
            $password = $_POST['loginPassword'];
            $errors = 'Invalid pseudo or password';

            $LoginManager = new LoginManager();
            $LoginManager -> loginUser($pseudo, $password,$errors);
            if(isset($_SESSION['pseudo'])) {
                $data = ['user' => $_SESSION];
                $this->redirectToRoute('home');
                return $this->render('home.html.twig', $data);
            }
            else{
                $data = ['errors' => $errors];
                return $this->render('login.html.twig', $data);
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
                $errors = $RegisterManager->registerUser($firstname, $lastname, $pseudo, $email, $password, $repeatPassword);
                if($errors === []) {
                    $data = ['user' => $_SESSION];
                    $this->redirectToRoute('home');
                    return $this->render('layout.html.twig', $data);
                }
                else{
                    $data = ['errors' => $errors];
                    return $this->render('register.html.twig', $data);
                }
        }
        return $this->render('register.html.twig');
    }
    public function UploadFilesAction()
    {
        if(isset($_FILES['inputFile'])){
            $UploadManager = new FilesManager();
            $messagesUpload = $UploadManager->uploadFiles();
            $data = ['file' => $_FILES,
                'user' => $_SESSION,
                'messagesUpload' => $messagesUpload];
            return $this->render('uploadFiles.html.twig',$data);
        }
        $data = ['user' => $_SESSION];
        return $this->render('uploadFiles.html.twig',$data);
    }
    public function viewFilesAction()
    {
        $FileManager = new FilesManager();
        $list = $FileManager->listFiles();
        $data = ['user' => $_SESSION,
                 'list' => $list];
        return $this->render('viewFiles.html.twig',$data);
    }
    public function logoutAction()
    {
        session_destroy();
        $this -> redirectToRoute('home');
        return $this->render('home.html.twig');
    }
}
