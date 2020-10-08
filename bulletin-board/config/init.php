<?php

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Tokyo');

define('BASE_URI_PATH',   '/yamada-challenge/challenge');
define('PROJECT_ROOT',    '/var/www/html' . BASE_URI_PATH);

define('CLASS_FILES_DIR', PROJECT_ROOT . '/classes');
define('LIB_FILES_DIR',   PROJECT_ROOT . '/lib');
define('HTML_FILES_DIR',  PROJECT_ROOT . '/html');
define('LOG_FILES_DIR',   PROJECT_ROOT . '/logs');

require_once(PROJECT_ROOT  . '/config/database.php');
require_once(PROJECT_ROOT  . '/lib/functions.php');
require_once(LIB_FILES_DIR . '/ClassLoader.php');

add_include_path(CLASS_FILES_DIR);
add_include_path(LIB_FILES_DIR);

spl_autoload_register(array('ClassLoader', 'autoload'));
