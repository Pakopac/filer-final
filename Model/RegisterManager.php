<?php

require_once('Cool/DBManager.php');

class RegisterManager
{
    public function registerUser($firstname, $lastname, $pseudo, $email,$password, $password_repeat)
    {
        /*$errors = [];
        $regexPassword = "^(?=.[a-z])(?=.[A-Z])(?=.\d)[a-zA-Z\d]{6,}$^";
        $regexEmail =  " /^[^\W][a-zA-Z0-9]+(.[a-zA-Z0-9]+)@[a-zA-Z0-9]+(.[a-zA-Z0-9]+)*.[a-zA-Z]{2,4}$/ ";

        if (!preg_match($regexEmail,$email)){
            $errors[] = 'Invalid Email';
        }
        if((strlen($pseudo) < 4) || (strlen($pseudo) > 20)){
            $errors[] = 'Pseudo too short or too long';
        }
        if(!preg_match($regexPassword,$password)){
            $errors[] = 'Password must have at least 6 characters with 1 letter uppercase and 1 number';
        }
        if($password !== $password_repeat){
            $errors[] = 'Password must be identical to the verification';
        }
        if($errors === []) {*/
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
        /*}
        else{
            ['errors' => $errors];
        }*/

    }
}