<?php

require_once('Cool/DBManager.php');

class FilesManager
{
    public function uploadFiles()
    {
        $messagesUpload = [];
        if(isset($_FILES['inputFile'])) {
            $uploaddir = $_GET['path'];
            $file = basename($_FILES['inputFile']['name']);
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

}