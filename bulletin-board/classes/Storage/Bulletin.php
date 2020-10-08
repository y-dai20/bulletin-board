<?php

class Storage_Bulletin extends Storage_Application
{
    protected $tableName     = 'bulletins';
    protected $validateRules = [
        'name'  => [
            'between' => ['min' => 3, 'max' => 16],
        ],
        'title' => [
            'required' => true,
            'between'  => ['min' => 10, 'max' => 32],
        ],
        'body'  => [
            'required' => true,
            'between'  => ['min' => 10, 'max' => 200],
        ],
        'image' => [
            'file'        => true,
            'mimetypes'   => ['jpeg', 'jpg', 'gif', 'png'],
            'maxFileSize' => 1024 * 1024,
        ],
        'pass'  => [
            'length'  => 4,
            'numbers' => true,
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

    public function softDelete($where = null, $whereArgs = [])
    {
        $this->update(
            ['is_deleted' => 1, 'image' => null],
            $where,
            $whereArgs
        );
    }

    public function deleteImageById($id)
    {
        $this->update(
            ['image' => null],
            'id = :id',
            ['id' => $id]
        );
    }

    public function recoveryById($id)
    {
        $this->update(
            ['is_deleted' => 0],
            'id = :id',
            ['id' => $id]
        );
    }

    public function validate($data)
    {
        $validator = new Validator($this->validateRules);

        return $validator->validate($data);
    }
}
