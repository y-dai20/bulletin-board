<?php

session_start();
require_once('../../config/init.php');

$controller = new Controller_User_Bulletin();
$controller->setParams(array_merge($_GET, $_POST));
$controller->setFiles($_FILES);
$controller->setEnvs($_SERVER);
$controller->setSession($_SESSION);
$controller->execute('delete');
