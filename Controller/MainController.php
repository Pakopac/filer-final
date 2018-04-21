<?php
require_once('Cool/BaseController.php');
require_once('Model/UserManager.php');
require_once('Model/FilesManager.php');
require_once('Model/LogManager.php');
session_start();
class MainController extends BaseController
{
    public function homeAction()
    {
        $LogManager = new LogManager();
        if(isset($_SESSION['pseudo'])) {
            $LogManager -> accessLog( $_SESSION['pseudo']." go to home \n");
            $data = ['user' => $_SESSION];
            return $this->render('home.html.twig',$data);
        }
        else {
            $LogManager->accessLog("User not logged in go to home\n");
        }
        return $this->render('home.html.twig');
    }
    public function loginAction()
    {
        $LogManager = new LogManager();
        if (isset($_SESSION['pseudo'])){
            $LogManager -> securityLog($_SESSION['pseudo']." try to go to login\n");
           $this->redirectToRoute('home');
        }
        else {
            $LogManager->accessLog("User not logged in go to login\n");
            if (!empty($_POST['loginPseudo']) && !empty($_POST['loginPassword'])) {
                $pseudo = htmlentities($_POST['loginPseudo']);
                $password = $_POST['loginPassword'];
                $errors = 'Invalid pseudo or password';

                $UserManager = new UserManager();
                $UserManager->loginUser($pseudo, $password, $errors);
                if (isset($_SESSION['pseudo'])) {
                    $LogManager -> accessLog($_SESSION['pseudo']." logged in\n");
                    $data = ['user' => $_SESSION];
                    $this->redirectToRoute('home');
                    return $this->render('home.html.twig', $data);
                } else {
                    $LogManager -> accessLog("User failed to connect\n");
                    $data = ['errors' => $errors];
                    return $this->render('login.html.twig', $data);
                }
            }
        }
        return $this->render('login.html.twig');
    }
    public function registerAction()
    {
        $LogManager = new LogManager();
        if (isset($_SESSION['pseudo'])){
            $LogManager -> securityLog($_SESSION['pseudo']." try to go to register\n");
            $this->redirectToRoute('home');
        }
        else {
            $LogManager -> accessLog("User not logged in go to register \n");
            if (!empty($_POST['firstname']) && !empty($_POST['lastname'])
                && !empty($_POST['pseudo']) && !empty($_POST['email'])
                && !empty($_POST['password']) && !empty($_POST['repeatPassword'])) {
                $firstname = htmlentities($_POST['firstname']);
                $lastname = htmlentities($_POST['lastname']);
                $pseudo = htmlentities($_POST['pseudo']);
                $email = htmlentities($_POST['email']);
                $password = $_POST['password'];
                $repeatPassword = $_POST['repeatPassword'];

                $UserManager = new UserManager();
                $errors = $UserManager->registerUser($firstname, $lastname, $pseudo, $email, $password, $repeatPassword);
                if ($errors === []) {
                    $LogManager -> accessLog($_SESSION['pseudo']." is register \n");
                    $data = ['user' => $_SESSION];
                    $this->redirectToRoute('home');
                    return $this->render('layout.html.twig', $data);
                } else {
                    $LogManager -> accessLog("User failed to register \n");
                    $data = ['errors' => $errors];
                    return $this->render('register.html.twig', $data);
                }
            }
            return $this->render('register.html.twig');
        }
    }

    public function logoutAction()
    {
        $LogManager = new LogManager();
        $LogManager -> accessLog($_SESSION['pseudo']." logged out \n");
        session_destroy();
        $this -> redirectToRoute('home');
        return $this->render('home.html.twig');
    }

    public function filesAction()
    {
        $LogManager = new LogManager();
        if(!isset($_SESSION['pseudo'])){
            $LogManager -> securityLog("User no logged in try to go to files \n");
            $this->redirectToRoute('home');
        }

        $path = $_GET['path'];
        if($path === 'files'){
            $LogManager -> securityLog($_SESSION['pseudo']." try to see files directory \n");
        }
        if(isset($_FILES['inputFile'])){
            $LogManager -> accessLog($_SESSION['pseudo']." upload a file \n");
            $FileManager = new FilesManager();
            list($messageUploadSuccess,$messageUploadError) = $FileManager->uploadFiles();
            $data = ['file' => $_FILES,
                'user' => $_SESSION,
               ];
           header('files&path='.$_GET['path'].'&user='.$_GET['user'].'#listFiles');
        }

        if (!empty($_POST['nameDirectory'])){
            $LogManager -> accessLog($_SESSION['pseudo']." create a directory \n");
            $nameDirectory = $_POST['nameDirectory'];
            $FileManager = new FilesManager();
            $FileManager->createDirectory($nameDirectory);
            return $this->render('files.html.twig');
        }

        $FileManager = new FilesManager();
        list($directory,$file) = $FileManager->listFiles();
        if(!empty($_POST['newName'])) {
            $FileManager->rename();
            $LogManager -> accessLog($_SESSION['pseudo']." rename a file or a directory \n");
        }
        if (!empty($_GET['delete'])) {
            $LogManager -> accessLog($_SESSION['pseudo']." delete a file or a directory \n");
            $FileManager->delete();
        }
        if (!empty($_GET['download'])) {
            $LogManager -> accessLog($_SESSION['pseudo']." download a file \n");
            $FileManager->download();
        }
        if(!empty($_GET['back'])) {
            $LogManager -> accessLog($_SESSION['pseudo']." back to previous directory \n");
            $FileManager->back();
        }
        if (isset($_GET['move']) && isset($_POST['moveFile'])) {
            $LogManager -> accessLog($_SESSION['pseudo']." move a file or a directory \n");
            $FileManager->move();
        }
        $data = ['user' => $_SESSION,
            'file' => $file,
            'directory' => $directory,
            'path' => $path,
         'messagesUploadSuccess' => $messageUploadSuccess,
            'messagesUploadError' => $messageUploadError];
        return $this->render('files.html.twig',$data);
    }

    public function editAction()
    {
        $LogManager = new LogManager();
        if(!isset($_SESSION['pseudo'])){
            $this->redirectToRoute('home');
            $LogManager -> securityLog("User not logged in try to go in edit \n");
        }
        else {
            $LogManager -> accessLog($_SESSION['pseudo']." edit a file \n");
            $FileManager = new FilesManager();
            $content = $FileManager->getEdit();
            $FileManager->edit($content);
            $data = ['user' => $_SESSION,
                'content' => $content];
            return $this->render('edit.html.twig', $data);
        }
    }
    public function viewAction()
    {   $FileManager = new FilesManager();
        $content = $FileManager -> getView();
        $data = ['user' => $_SESSION,
            'content' => $content];
        return $this->render('view.html.twig',$data);
    }
}