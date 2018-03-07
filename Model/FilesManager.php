<?php

require_once('Cool/DBManager.php');

class FilesManager
{
    public function uploadFiles()
    {
        $messagesUpload = [];
        if(isset($_FILES['inputFile'])) {
            $uploaddir = 'files/' . $_SESSION['pseudo'] . '/';
            $file = basename($_FILES['inputFile']['name']);
        if(file_exists($uploaddir.$file)){
            $messagesUpload[] = 'Error: File already exist';
        }
        elseif(move_uploaded_file($_FILES['inputFile']['tmp_name'], $uploaddir . $file)) {
            $messagesUpload[] = 'File send !';
        }
        else {
            $messagesUpload[] = 'Error: File not send !';
        }
    }
    return $messagesUpload;
    }

    public function listFiles()
    {
        $list = [];
        $dir = opendir('files/' . $_SESSION['pseudo'] . '');
        while(false !== ($file = readdir($dir))){
            if($file !== '.' && $file !=='..') {
                $list[] = $file;
            }
        }
        return $list;
    }

    public function createDirectory($nameDirectory, $pathDirectory)
    {
        mkdir('./files/'.$_SESSION['pseudo']. '/' . $pathDirectory .'/' . $nameDirectory);
    }
}