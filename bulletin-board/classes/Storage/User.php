<?php

class Storage_User extends Storage_Application
{
    protected $tableName = 'users';

    public function verifyPassword($pass, $hashedPass)
    {
        return password_verify($pass, $hashedPass);
    }

    public function insert($data)
    {
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        parent::insert($data);
    }
}
