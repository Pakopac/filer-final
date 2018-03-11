<?php

require_once('Cool/DBManager.php');

class LogManager
{
    public function accessLog($message)
    {
        file_put_contents('log/access.log', $message, FILE_APPEND);
    }

    public function securityLog($message)
    {
        file_put_contents('log/security.log', $message, FILE_APPEND);
    }
}