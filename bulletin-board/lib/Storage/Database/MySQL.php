<?php

class Storage_Database_MySQL extends Storage_Database
{
  public function __construct($config = array())
  {
    if (!isset($config['charset'])) {
      $config['charset'] = 'utf8';
    }

    parent::__construct($config);
  }

  public function fetch($tableName, $column = null, $where = null, $whereArgs = [], $order = null, $offset = null, $limit = null)
  {
    if (empty($column)) {
      $column = '*';
    }

    $sql = "SELECT {$column} FROM {$tableName}";

    if (!empty($where) || !empty($whereArgs)) {
      $sql = $sql . " WHERE " . $where;
    }

    if (!empty($order)) {
      $sql = $sql . ' ORDER BY ' . $order;
    }

    if (!empty($limit)) {
      $sql = $sql . ' LIMIT ' . $limit;
    }

    if (!empty($offset)) {
      $sql = $sql . ' OFFSET ' . $offset;
    }

    $stmt = $this->dbh->prepare($sql);

    if (!empty($where) && !empty($whereArgs)) {
      $this->bindValues($stmt, $whereArgs);
    }

    $result = $stmt->execute();

    if ($result === false) {
      throw new Exception(__METHOD__ . '() ' . $this->dbh->errorInfo()[2]);
    }

    $rows = $stmt->fetchAll();

    return $rows;
  }

  public function getCount($tableName, $column = null, $where = null, $whereArgs = [])
  {
    if (empty($column)) {
      $column = '*';
    }

    $result = $this->fetch($tableName, "COUNT({$column}) AS c", $where, $whereArgs);

    if (isset($result[0]['c'])) {
      return $result[0]['c'];
    } else {
      throw new Exception(__METHOD__ . '() failed.');
    }
  }

  public function insert($tableName, $data)
  {
    if (empty($data)) {
      throw new Exception(__METHOD__ . '() data is empty.');
    }

    $sql     = "INSERT INTO {$tableName}";
    $keys    = array_keys($data);
    $columns = implode(', ', $keys);
    $values  = ':' . implode(', :', $keys);

    $sql  = $sql . "({$columns}) VALUES ({$values})";
    $stmt = $this->dbh->prepare($sql);
    $this->bindValues($stmt, $data);
    $result = $stmt->execute();

    if ($result === false) {
      throw new Exception(__METHOD__ . '() ' . $this->dbh->errorInfo()[2]);
    }

    return $result;
  }

  public function update($tableName, $data, $where = null, $whereArgs = [])
  {
    if (empty($data)) {
      throw new Exception(__METHOD__ . '() data is empty.');
    }

    $sql = "UPDATE {$tableName}";

    $values = array();
    foreach ($data as $key => $value) {
      $values[] = $key . ' = :' . $key;
    }

    $values = implode(', ', $values);
    $sql = $sql . " SET {$values}";

    if (!empty($where) || !empty($whereArgs)) {
      $sql = $sql . " WHERE " . $where;
    }

    $stmt = $this->dbh->prepare($sql);

    if (!empty($where) && !empty($whereArgs)) {
      $this->bindValues($stmt, array_merge($data, $whereArgs));
    }

    $result = $stmt->execute();

    if ($result === false) {
      throw new Exception(__METHOD__ . '() ' . $this->dbh->errorInfo()[2]);
    }

    return $result;
  }

  public function delete($tableName, $where = null, $whereArgs = [])
  {
    $sql = "DELETE FROM {$tableName}";

    if (!empty($where) || !empty($whereArgs)) {
      $sql = $sql . " WHERE " . $where;
    }

    $stmt = $this->dbh->prepare($sql);
    if (!empty($where) && !empty($whereArgs)) {
      $this->bindValues($stmt, $whereArgs);
    }
    $result = $stmt->execute();

    if ($result === false) {
      throw new Exception(__METHOD__ . '() ' . $this->dbh->errorInfo()[2]);
    }

    return $result;
  }

  protected function bindValues($stmt, array $args)
  {
    foreach ($args as $key => $value) {
      $stmt->bindValue(":{$key}", $value, $this->getParameterType($value));
    }
  }

  protected function getParameterType($parameter)
  {
    $type = gettype($parameter);
    if ($type === 'integer') {
        return PDO::PARAM_INT;
    } elseif ($type === 'string') {
        return PDO::PARAM_STR;
    } elseif ($type === 'boolean') {
        return PDO::PARAM_BOOL;
    }

    return PDO::PARAM_NULL;
  }
}
