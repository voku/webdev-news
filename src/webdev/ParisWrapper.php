<?php

namespace webdev;

use idiorm\orm\ORM;

/**
 * ParisWrapper: a Wrapper-Class for "Paris"-ORM
 *
 * Info:
 * - this class is implemented as a "Multi Singleton pattern"
 * - depends on "Paris"-ORM
 */
class ParisWrapper
{

  /**
   * @var string
   */
  private $hostname = '';

  /**
   * @var string
   */
  private $username = '';

  /**
   * @var string
   */
  private $password = '';

  /**
   * @var string
   */
  private $database = '';

  /**
   * @var int
   */
  private $port = 3306;

  /**
   * @var string
   */
  private $charset = 'utf84mb';

  /**
   * @var string
   */
  private $driver = 'mysql';

  /**
   * @var string
   */
  private $unix_socket = '';

  /**
   * __construct()
   *
   * @param $hostname
   * @param $username
   * @param $password
   * @param $database
   * @param $port
   * @param $charset
   * @param $driver
   * @param $unix_socket
   */
  private function __construct($hostname, $username, $password, $database, $port, $charset, $driver, $unix_socket)
  {
    $this->_loadConfig($hostname, $username, $password, $database, $port, $charset, $driver, $unix_socket);

    $this->connect();
  }

  /**
   * load the config
   *
   * @param $hostname
   * @param $username
   * @param $password
   * @param $database
   * @param $port
   * @param $charset
   * @param $driver
   * @param $unix_socket
   *
   * @return bool
   */
  private function _loadConfig($hostname, $username, $password, $database, $port, $charset, $driver, $unix_socket)
  {
    $this->hostname = $hostname;
    $this->username = $username;
    $this->password = $password;
    $this->database = $database;

    if ($charset) {
      $this->charset = $charset;
    }

    if ($port) {
      $this->port = (int)$port;
    }

    if ($driver) {
      $this->driver = $driver;
    }

    if ($unix_socket) {
      $this->unix_socket = $unix_socket;
    }

    return $this->showConfigError();
  }

  /**
   * show config error and throw a exception
   *
   * @return bool
   *
   * @throws \Exception
   */
  public function showConfigError()
  {

    if (
        !$this->hostname
        ||
        !$this->username
        ||
        !$this->database
        ||
        (!$this->password && $this->password != '')
    ) {

      if (!$this->hostname) {
        throw new \Exception('no sql-hostname');
      }

      if (!$this->username) {
        throw new \Exception('no sql-username');
      }

      if (!$this->database) {
        throw new \Exception('no sql-database');
      }

      if (!$this->password) {
        throw new \Exception('no sql-password');
      }

      return false;
    } else {
      return true;
    }
  }

  /**
   * connect()
   */
  public function connect()
  {
    ORM::configure($this->driver . ':host=' . $this->hostname . ';dbname=' . $this->database . ';port=' . $this->port . ';charset=' . $this->charset);
    ORM::configure('username', $this->username);
    ORM::configure('password', $this->password);

    //Model::$auto_prefix_models = '\\' . getConfigDBPrefix() . '\\';
  }

  /**
   * getInstance()
   *
   * @param string $hostname
   * @param string $username
   * @param string $password
   * @param string $database
   * @param string $port
   * @param string $charset
   * @param string $driver
   * @param string $unix_socket
   *
   * @return ParisWrapper
   */
  public static function getInstance($hostname = '', $username = '', $password = '', $database = '', $port = '', $charset = '', $driver = '', $unix_socket = '')
  {
    /**
     * @var $instance ParisWrapper[]
     */
    static $instance;

    /**
     * @var $firstInstance ParisWrapper
     */
    static $firstInstance;

    if ($hostname . $username . $password . $database . $port . $charset . $driver . $unix_socket == '') {
      if (null !== $firstInstance) {
        return $firstInstance;
      }
    }

    $connection = md5($hostname . $username . $password . $database . $port . $charset . $driver . $unix_socket);

    if (null === $instance[$connection]) {
      $instance[$connection] = new self(
          $hostname,
          $username,
          $password,
          $database,
          $port,
          $charset,
          $driver,
          $unix_socket
      );

      if (null === $firstInstance) {
        $firstInstance = $instance[$connection];
      }
    }

    return $instance[$connection];
  }

}