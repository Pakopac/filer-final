<?php

require_once('Cool/DBManager.php');

class FilesManager
{
    public function uploadFiles()
    {
        $messagesUpload = [];
        if(isset($_FILES['inputFile'])) {
            $uploaddir = $_GET['path'];
            if (!empty($_POST['nameFile'])){
                $ext = new SplFileInfo($_FILES['inputFile']['name']);
                $file = basename($_POST['nameFile'].'.'.$ext->getExtension());
            }
            else {
                $file = basename($_FILES['inputFile']['name']);
            }
        if(file_exists($uploaddir.'/'.$file)){
            $messagesUpload[] = 'Error: File already exist';
        }
        elseif(move_uploaded_file($_FILES['inputFile']['tmp_name'], $uploaddir. '/' . $file)) {
            $messagesUpload[] = 'File send !';
        }
        else {
            $messagesUpload[] = 'Error: File not send !';
        }
    }
    header('Location: ?action=files&path='.$_GET['path']);
    return $messagesUpload;
    }

    public function listFiles()
    {
        $list = [];
        $path = $_GET['path'];
            if(is_dir($path)) {
                $file = array_diff(scandir($path), array('.', '..'));
                foreach ($file as $value) {
                    $list[] = $value;
                }
            }
            else{
                header('Location:'.$_GET['path']);
            }
        return $list;
    }

    public function createDirectory($nameDirectory)
    {
        mkdir($_GET['path'].'/'.$nameDirectory);
        header('Location: ?action=files&path='.$_GET['path']);
    }

    public function rename()
    {
        if(!empty($_POST['newName'])){
            $path = $_GET['path'];
            $name = $_GET['name'];
            if(is_file($path.'/'.$name)){
                $ext = new SplFileInfo($name);;
                rename($path.'/'.$name, $path.'/'.$_POST['newName'].'.'.$ext->getExtension());
            }
            rename($path.'/'.$name, $path.'/'.$_POST['newName']);
            header('Location: ?action=files&path='.$path.'&name='.$_POST['newName']);
        }
    }
}