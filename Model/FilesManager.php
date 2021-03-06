<?php

require_once('Cool/DBManager.php');

class FilesManager
{
    public function uploadFiles()
    {
        $messagesUploadSuccess = '';
        $messagesUploadError = '';
        if(isset($_FILES['inputFile'])) {
            $uploaddir = $_GET['path'];
            if (!empty($_POST['nameFile'])){
                $name = str_replace('/','',$_POST['nameFile']);
                $name = str_replace('..','',$_POST['nameFile']);
                $ext = new SplFileInfo($_FILES['inputFile']['name']);
                $file = basename($name.'.'.$ext->getExtension());
            }
            else {
                $file = basename($_FILES['inputFile']['name']);
            }
            if(file_exists($uploaddir.'/'.$file)){
                $messagesUploadError = 'Error: File already exist';
            }
            elseif(move_uploaded_file($_FILES['inputFile']['tmp_name'], $uploaddir. '/' . $file)) {
                $messagesUploadSuccess = 'File send !';
            }
            else {
                $messagesUploadError = 'Error: File not send !';
            }
            return [$messagesUploadSuccess,$messagesUploadError];
        }
    }

    public function listFiles()
    {
        $directory = [];
        $file = [];
        $path = $_GET['path'];
        $user = $_GET['user'];
        if($user == $_SESSION['pseudo']) {
            if (is_dir($path) && $path !== 'files') {
                $list = array_diff(scandir($path), array('.', '..'));
                foreach ($list as $value) {
                    if (is_dir($path . '/' . $value)) {
                        $directory[] = $value;
                    } elseif (is_file($path . '/' . $value)) {
                        $file[] = $value;
                    }
                }
            }
            return [$directory, $file];
        }
        else{
            return [];
        }

    }

    public function createDirectory($nameDirectory)
    {
        mkdir($_GET['path'].'/'.$nameDirectory);
        header('Location: ?action=files&path='.$_GET['path'].'&user='.$_GET['user'].'#listFiles');
    }

    public function rename()
    {
        if(!empty($_POST['newName'])){
            $path = $_GET['path'];
            $name = $_GET['name'];
            if(is_file($path.'/'.$name)){
                $newName = str_replace('/','',$_POST['newName']);
                $newName = str_replace('..','',$_POST['newName']);
                $ext = new SplFileInfo($name);;
                rename($path.'/'.$name, $path.'/'.$newName.'.'.$ext->getExtension());
            }
            rename($path.'/'.$name, $path.'/'.$_POST['newName']);
            header('Location: ?action=files&path='.$path.'&user='.$_GET['user'].'#listFiles');
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
            header('Location: ?action=files&path='.$path.'&user='.$_GET['user'].'#listFiles');
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
        if(!empty($_GET['back'])) {
            $path = $_GET['path'];
            if ($path !== 'files/' . $_SESSION['pseudo']) {
                strrchr($path, '/');
                $path = str_replace(strrchr($path, '/'), '', $path);
                header('Location:?action=files&path=' . $path.'&user='.$_GET['user']);
            }
            else{
                header('Location:?action=files&path='.$path.'&user='.$_GET['user']);
            }
        }
    }
    public function move()
    {
        if (isset($_GET['move']) && isset($_POST['moveFile'])) {
            $path = $_GET['path'];
            $fileName = $_GET['move'];
            $newPath = $_POST['moveFile'];
            if (is_dir($path.'/'.$fileName)) {
                $dir = $path . '/' . $fileName;
                function moveFile($dir)
                {
                    $file = array_diff(scandir($dir), array('.', '..'));
                    foreach ($file as $value) {

                        if (is_file($dir . '/' . $value)) {
                            $fileName = $_GET['move'];
                            $newPath = $_POST['moveFile'];
                            rename($dir . '/' . $value,$newPath.'/'.$fileName.'/'.$value);
                        }
                        else {
                            if(!empty(moveFile($dir . '/' . $value)));
                            else {
                                $fileName = $_GET['move'];
                                $newPath = $_POST['moveFile'];
                                rename($dir . '/' . $value,$newPath.'/'.$fileName.'/'.$value);
                            }
                        }
                    }
                }
                rename($path.'/'.$fileName, 'files/'.$_SESSION['pseudo'].'/'.$newPath.'/'.$fileName);
                moveFile($dir);
            }
            rename($path.'/'.$fileName, 'files/'.$_SESSION['pseudo'].'/'.$newPath.'/'.$fileName);
        header('Location: ?action=files&path='.$path.'&user='.$_GET['user'].'#listFiles');
        }
    }
    public function getEdit()
    {
        if (isset($_GET['edit'])){
            $path = $_GET['path'];
            $fileName = $_GET['edit'];
            $ext = new SplFileInfo($fileName);
            if($ext->getExtension() === 'txt') {
                $getEdit = fopen($path . '/' . $fileName, "r");
                $content = fgets($getEdit, 255);
                fclose($getEdit);
                return $content;
            }
            else{
                header('Location: ?action=files&path='.$path.'&user='.$_GET['user']);
            }
        }
    }
    public function edit()
    {
        if(isset($_POST['editFile'])) {
            $path = $_GET['path'];
            $fileName = $_GET['edit'];
            $edit = fopen($path.'/'.$fileName,"w");
            fwrite($edit,$_POST['editFile']);
            fclose($edit);
            header('Location: ?action=files&path='.$path.'&user='.$_GET['user']);
        }
    }
    public function getView()
    {
        $path = $_GET['path'];
        $fileName = $_GET['file'];
        $ext = new SplFileInfo($fileName);
        if ($ext->getExtension() === 'txt') {
            $content = file_get_contents($path);
            return $content;
        }
        elseif ($ext->getExtension() === 'png') {
            header('Content-Type: image/x-png');
            $content =  readfile($path);
            return $content;
        }
        elseif ($ext->getExtension() === 'jpg') {
            header('Content-Type: image/jpg');
            $content =  readfile($path);
            return $content;
        }
        elseif ($ext->getExtension() === 'mp3') {
            header('Content-Type: audio/mp3');
            $content =  readfile($path);
            return $content;
        }
        elseif ($ext->getExtension() === 'wav') {
            header('Content-Type: audio/wav');
            $content =  readfile($path);
            return $content;
        }
        elseif ($ext->getExtension() === 'mp4') {
            header('Content-Type: video/mp4');
            $content =  readfile($path);
            return $content;
        }
        elseif ($ext->getExtension() === 'avi') {
            header('Content-Type: video/avi');
            $content =  readfile($path);
            return $content;
        }
        else{
            header("Content-Description: File Transfer");
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=".basename($path));

            readfile ($path);
            exit();
        }

    }
}