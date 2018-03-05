<?php

require_once('Cool/DBManager.php');

class UploadManager
{
    public function uploadFiles()
    {
        if(isset($_FILES['inputFile'])) {
            $uploaddir = 'files/' . $_SESSION['pseudo'] . '/';
            $file = basename($_FILES['inputFile']['name']);
            move_uploaded_file($_FILES['inputFile']['tmp_name'], $uploaddir . $file);
        }
    }
}