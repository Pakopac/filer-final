<?php

require_once('Cool/DBManager.php');

class LoginManager
{
    public function loginUser($pseudo, $password)
    {
        $dbm = DBManager::getInstance();
        $pdo = $dbm->getPdo();

        $result = $pdo->prepare("SELECT * FROM users WHERE password = :password AND pseudo = :pseudo");
        $result->bindParam(':pseudo', $pseudo);
        $result->bindParam(':password', $password);
        $result->execute();
        $user = $result -> fetch();

        if($user === false && $pseudo !== 'undefined' && $password !== 'undefined'){
          echo 'invalid';
        }

        else{
            session_start();
            $_SESSION['id'] = $user['id'];
            $_SESSION['pseudo'] = $user['pseudo'];
        }
    }
}