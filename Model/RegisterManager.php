<?php

require_once('Cool/DBManager.php');

class RegisterManager
{
    public function registerUser($firstname, $lastname, $pseudo, $email,$password)
    {
            $dbm = DBManager::getInstance();
            $pdo = $dbm->getPdo();

            $stmt = $pdo->prepare("INSERT INTO `users` (`id`, `firstname`, `lastname`, `pseudo`, `email`, `password`) VALUES (NULL, :firstname, :lastname, :pseudo, :email, :password)");
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':pseudo', $pseudo);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);

            $stmt->execute();

            $result = $pdo->prepare("SELECT * FROM users WHERE password = :password AND pseudo = :pseudo");
            $result->bindParam(':pseudo', $pseudo);
            $result->bindParam(':password', $password);
            $result->execute();
            $user = $result->fetch();
            $_SESSION['id'] = $user['id'];
            $_SESSION['pseudo'] = $user['pseudo'];
    }
}