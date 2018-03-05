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
        $_SESSION = $user;
    }
}