<?php

abstract class Storage_Database
{
  protected $dbh = null;

  protected $config = array(
    'dsn'      => '',
    'port'     => '',
    'user'     => '',
    'password' => '',
    'options'  => [],
  );

  public function __construct($config = array())
  {
    $this->config = array_merge($this->config, $config);

    if (function_exists('get_db_config')) {
      $this->config = array_merge($this->config, get_db_config());
    }

    $config = $this->config;
    $this->dbh = new PDO($config['dsn'], $config['user'], $config['password'], $config['options']);
  }
}
