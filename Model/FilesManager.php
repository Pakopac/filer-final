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
        $directory = [];
        $file = [];
        $path = $_GET['path'];
            if(is_dir($path)) {
                $list = array_diff(scandir($path), array('.', '..'));
                foreach ($list as $value) {
                    if(is_dir($path.'/'.$value)) {
                        $directory[] = $value;
                    }
                    elseif(is_file($path.'/'.$value)){
                        $file[] = $value;
                    }
                }
            }
        return [$directory,$file];
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
            header('Location: ?action=files&path='.$path.'#');
        }
    }
    public function delete()
    {
        if (!empty($_GET['delete'])) {
            $path = $_GET['path'];
            $delete = $_GET['delete'];
            if (is_dir($path . '/' . $delete)) {
                $dir = $path . '/' . $delete;
                function deleteFile($dir)
                {
                    $file = array_diff(scandir($dir), array('.', '..'));
                    foreach ($file as $value) {

                        if (is_file($dir . '/' . $value)) {
                            unlink($dir . '/' . $value);
                        }
                        else {
                            if(!empty(deleteFile($dir . '/' . $value)));
                                else {
                                    rmdir($dir . '/' . $value);
                                }
                            }
                    }
                }
                deleteFile($dir);
                rmdir($path . '/' . $delete);
            }
            unlink($path . '/' . $delete);
            header('Location: ?action=files&path='.$path);
        }
    }

    public function download()
    {
        if (!empty($_GET['download'])) {
            $path = $_GET['path'];
            $file = $_GET['download'];

            header("Content-Description: File Transfer");
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=".basename($path.'/'.$file));

            readfile ($path.'/'.$file);
            exit();
        }
    }

    public function back()
    {
        if(!empty($_GET['back'])){
            $path = $_GET['path'];
            strrchr($path,'/');
            $path = str_replace(strrchr($path,'/'),'',$path);
            header('Location:?action=files&path='.$path);
        }
    }
}