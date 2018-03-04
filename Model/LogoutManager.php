<?php

require_once('Cool/DBManager.php');

class LogoutManager
{
    public function logout()
    {
        session_destroy();
    }
}