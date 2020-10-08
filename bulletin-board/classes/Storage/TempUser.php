<?php

class Storage_TempUser extends Storage_Application
{
    protected $tableName     = 'temp_users';
    protected $validateRules = [
        'name' => [
            'required' => true,
            'between'  => ['min' => 3, 'max' => 16],
        ],
        'email' => [
            'required'  => true,
            'maxLength' => 255,
            'email'     => true,
        ],
        'pass' => [
            'required' => true,
            'between'  => ['min' => 8, 'max' => 16],
        ],
    ];

    protected function hashPassword($pass)
    {
        return password_hash($pass, PASSWORD_DEFAULT);
    }

    public function verifyPassword($pass, $hashedPass)
    {
        return password_verify($pass, $hashedPass);
    }

    public function insert($data)
    {
        if (isset($data['pass'])) {
            $data['pass'] = $this->hashPassword($data['pass']);
        }

        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        parent::insert($data);
    }

    public function validate($data)
    {
        $validator = new Validator($this->validateRules);

        return $validator->validate($data);
    }
}
