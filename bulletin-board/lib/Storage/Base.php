<?php

abstract class Storage_Base
{
    protected $database  = null;
    protected $tableName = '';

    public function __construct()
    {
        $this->database = new Storage_Database_MySQL();
    }

    public function fetch($column = null, $where = null, $whereArgs = [], $order = null, $offset = null, $limit = null)
    {
        return $this->database->fetch($this->tableName, $column, $where, $whereArgs, $order, $offset, $limit);
    }

    public function getCount($column = null, $where = null, $whereArgs = [])
    {
        return $this->database->getCount($this->tableName, $column, $where, $whereArgs);
    }

    public function insert($data)
    {
        return $this->database->insert($this->tableName, $data);
    }

    public function update($data, $where = null, $whereArgs = [])
    {
        return $this->database->update($this->tableName, $data, $where, $whereArgs);
    }

    public function delete($where = null, $whereArgs = [])
    {
        return $this->database->delete($this->tableName, $where, $whereArgs);
    }
}
