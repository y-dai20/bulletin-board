<?php

class Storage_Manager extends Storage_Application
{
    protected $tableName = 'managers';

    public function verifyPassword($pass, $hashedPass)
    {
        return password_verify($pass, $hashedPass);
    }
}
