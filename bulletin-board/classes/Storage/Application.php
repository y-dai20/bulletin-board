<?php

class Storage_Application extends Storage_Base
{
    public function updateById($id, $data)
    {
        $this->update($data, 'id = :id',['id' => $id]);
    }

    public function deleteById($id)
    {
        $this->delete('id = :id', ['id' => $id]);
    }
}
