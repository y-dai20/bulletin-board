<?php

abstract class Controller_Base
{
  protected $method = 'GET';

  protected $params  = array();
  protected $files   = array();
  protected $session = array();

  protected $logger = null;

  protected $envs = array(
    'http-host'       => 'localhost',
    'server-name'     => 'localhost',
    'server-port'     => '80',
    'server-protocol' => 'HTTP/1.0',
    'remote-addr'     => '127.0.0.1',
    'request-uri'     => '/',
  );

  public function setParams(array $params)
  {
    $this->params = $params;
  }

  public function setParam($key, $value)
  {
    $this->params[$key] = $value;
  }

  public function getParams()
  {
    return $this->params;
  }

  // study: getParam($key, $default = null) みたいにしたら色んな所でコードが楽に書けたりしない？
  public function getParam($key)
  {
    $params = $this->params;
    if (isset($params[$key]) && $params[$key] !== '') {
      if (is_array($params[$key])) {
        return $params[$key];
      } else {
        return trim($params[$key]);
      }
    }
  }

  public function setSession(array $session)
  {
    $this->session = $session;
  }

  public function getSession($key)
  {
    $session = $this->session;

    if (isset($session[$key]) && !empty($session[$key])) {
      return $session[$key];
    }
  }

  public function setEnvs(array $envs)
  {
    foreach ($envs as $key => $value) {
      $this->setEnv($key, $value);
    }

    if ($_method = $this->getEnv('Request-Method')) {
      $this->setMethod($_method);
    }
  }

  public function setEnv($key, $value)
  {
    $this->envs[$this->normalizeEnvKey($key)] = $value;
  }

  public function getEnvs()
  {
    return $this->envs;
  }

  public function getEnv($key)
  {
    $_key = $this->normalizeEnvKey($key);

    if (isset($this->envs[$_key])) {
      return $this->envs[$_key];
    }
  }

  public function setMethod($method)
  {
    $_method = strtoupper($method);

    if (in_array($_method, array('GET', 'POST', 'PUT', 'DELETE'))) {
      $this->method = $_method;
    } else {
      trigger_error(__METHOD__ . '() Invalid method: ' . $method, E_USER_ERROR);
    }
  }

  public function getMethod()
  {
    return $this->method;
  }

  public function setFiles(array $files)
  {
    $this->files = $files;
  }

  public function getFile($key)
  {
    $files = $this->files;
    if (isset($files[$key]) && !empty($files[$key])) {
      $file = $files[$key];
      if (!empty($file['tmp_name']) && $file['size'] >= 1) {
        $file['data'] = file_get_contents($file['tmp_name']);

        return $file;
      }
    }
  }

  public function setUp()
  {
    $this->logger = new Logger();

    set_error_handler(array($this, 'errorHandler'));
  }

  public function execute($action)
  {
    try {
      $this->setUp();

      if (!method_exists($this, $action)) {
        throw new Exception(__METHOD__ . "() Action not found. '{$action}'");
      }

      $this->$action();
    } catch (Exception $e) {
      $this->err500($e->getMessage());
    }
  }

  public function redirect($uri, $params = array(), $exit = true)
  {
    if (!empty($params)) {
      $glue = (strpos($uri, '?') === false) ? '?' : '&';
      $uri .= $glue . http_build_query($params, '', '&');
    }

    header('Location: ' .  BASE_URI_PATH . '/' . $uri);

    if ($exit) {
      exit;
    }
  }

  public function errorHandler($errno, $errstr, $errfile, $errline)
  {
    $message = $errstr;

    if (!empty($errfile)) {
      $message .= ' file: '  . $errfile;
    }

    if (!empty($errline)) {
      $message .= ' line: '  . $errline;
    }

    $this->log($message, $errno);

    return false;
  }

  public function log($message, $errType = E_ALL)
  {
    if ($this->logger) {
      $message = $this->getEnv('Remote-Addr') . ' '
               . $this->getEnv('Request-Uri') . ' '
               . $message;

      $this->logger->write($message, $errType);
    }
  }

  public function err400($message = "", $exit = true)
  {
    $protocol = $this->getEnv('Server-Protocol');
    header("{$protocol} 400 Bad Request");

    $this->render('error/400.php', array(
      'message'    => $message,
      'requestUri' => $this->getEnv('Request-Uri'),
    ));

    $this->log('400 Bad Request ' . $message, E_WARNING);

    if ($exit) {
      exit;
    }
  }

  public function err403($message = "", $exit = true)
  {
    $protocol = $this->getEnv('Server-Protocol');
    header("{$protocol} 403 Bad Request");

    $this->render('error/403.php', array(
      'message'    => $message,
      'requestUri' => $this->getEnv('Request-Uri'),
    ));

    $this->log('403 Bad Request ' . $message, E_WARNING);

    if ($exit) {
      exit;
    }
  }

  public function err404($message = "", $exit = true)
  {
    $protocol = $this->getEnv('Server-Protocol');
    header("{$protocol} 404 Not Found");

    $this->render('error/404.php', array(
      'message'    => $message,
      'requestUri' => $this->getEnv('Request-Uri'),
    ));

    $this->log('404 Not Found ' . $message, E_NOTICE);

    if ($exit) {
      exit;
    }
  }

  public function err500($message = "", $exit = true)
  {
    $protocol = $this->getEnv('Server-Protocol');
    header("{$protocol} 500 Internal Server Error");

    $this->render('error/500.php', array(
      'message'    => $message,
      'requestUri' => $this->getEnv('Request-Uri'),
    ));

    $this->log('500 Internal Server Error ' . $message, E_ERROR);

    if ($exit) {
      exit;
    }
  }

  protected function render($fileName, $data = array())
  {
    if ($template = $this->getSessionlate($fileName)) {
      extract(array_merge(get_object_vars($this), $data), EXTR_OVERWRITE);
      include($template);
    } else {
      trigger_error(__METHOD__ . '() Template not found: ' . $fileName, E_USER_ERROR);
    }
  }

  protected function getSessionlate($fileName)
  {
    $path = HTML_FILES_DIR . '/' . $fileName;

    if (file_exists($path)) {
      return $path;
    }
  }

  protected function normalizeEnvKey($key)
  {
    return strtolower(str_replace('_', '-', $key));
  }
}
