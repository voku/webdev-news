<?php
use kint\Kint;
use voku\cache\Cache;
use voku\helper\AntiXSS;

/**
 * clear the string || array via AntiXSS::xss_clean();
 *
 * @param array $array          WARNING: this is a reference not a variable!!!
 * @param       $antiXSS        AntiXSS
 */
function clearXss(array &$array, AntiXSS $antiXSS)
{
  $cache = new Cache(null, null, false, true);
  $cacheIsReady = $cache->getCacheIsReady();

  foreach ($array as &$value) {
    if (is_array($value)) {
      clearXss($value, $antiXSS);
    } else {
      $cacheKey = 'antiXss_' . md5($value);

      if (
          $cacheIsReady === true
          &&
          $cache->existsItem($cacheKey)
      ) {
        $value = $cache->getItem($cacheKey);
      } else {
        $value = $antiXSS->xss_clean($value);

        if ($cacheIsReady === true) {
          $cache->setItem($cacheKey, $value);
        }
      }
    }
  }
}

/**
 * Get GET input
 *
 * @param string $key
 * @param mixed  $filter
 * @param bool   $fillWithEmptyString
 *
 * @return mixed
 */
function get($key = null, $filter = null, $fillWithEmptyString = false)
{
  if (!$key) {
    if (function_exists('filter_var_array')) {
      return $filter ? filter_var_array($_GET, $filter) : $_GET;
    } else {
      return $_GET;
    }
  }

  if (isset($_GET[$key])) {

    if (FILTER_XSS === $filter) {
      // reset the filter
      $filter = null;
      // anti-xss
      $antiXss = new AntiXSS();
      $_GET[$key] = $antiXss->xss_clean($_GET[$key]);
    }

    if (function_exists('filter_var')) {
      return $filter ? filter_var($_GET[$key],  $filter) : $_GET[$key];
    } else {
      return $_GET[$key];
    }

  } else if ($fillWithEmptyString === true) {
    return '';
  }

  return null;
}

/**
 * Get POST input
 *
 * @param string $key
 * @param mixed  $filter
 * @param bool   $fillWithEmptyString
 *
 * @return mixed
 */
function post($key = null, $filter = null, $fillWithEmptyString = false)
{
  if (!$key) {
    if (function_exists('filter_var_array')) {
      return $filter ? filter_var_array($_POST, $filter) : $_POST;
    } else {
      return $_POST;
    }
  }

  if (isset($_POST[$key])) {

    if (FILTER_XSS === $filter) {
      // reset the filter
      $filter = null;
      // anti-xss
      $antiXss = new AntiXSS();
      $_POST[$key] = $antiXss->xss_clean($_POST[$key]);
    }

    if (function_exists('filter_var')) {
      return $filter ? filter_var($_POST[$key], $filter) : $_POST[$key];
    } else {
      return $_POST[$key];
    }

  } else if ($fillWithEmptyString === true) {
    return '';
  }

  return null;
}

/**
 * Get GET_POST input
 *
 * @param string $key
 * @param mixed  $filter
 * @param bool   $fillWithEmptyString
 * @param bool   $caseInsensitive
 *
 * @return mixed
 */
function get_post($key = null, $filter = null, $fillWithEmptyString = false, $caseInsensitive = false)
{
  // WARNING: if you filter the input, please make sure to overwrite this variable
  if (!isset($GLOBALS['_GET_POST'])) {
    $GLOBALS['_GET_POST'] = array_merge($_GET, $_POST);
  }

  if (!$key) {
    if (function_exists('filter_var_array')) {
      return $filter ? filter_var_array($GLOBALS['_GET_POST'], $filter) : $GLOBALS['_GET_POST'];
    } else {
      return $GLOBALS['_GET_POST'];
    }
  }

  if ($caseInsensitive === true) {
    foreach ($GLOBALS['_GET_POST'] as $keyTmp => $valueTmp) {
      if (strtolower($keyTmp) == strtolower($key)) {
        $key = $keyTmp;
      }
    }
  }

  if (isset($GLOBALS['_GET_POST'][$key])) {

    if (FILTER_XSS === $filter) {
      // reset the filter
      $filter = null;
      // anti-xss
      $antiXss = new AntiXSS();
      $GLOBALS['_GET_POST'][$key] = $antiXss->xss_clean($GLOBALS['_GET_POST'][$key]);
    }

    if (function_exists('filter_var')) {
      return $filter ? filter_var($GLOBALS['_GET_POST'][$key], $filter) : $GLOBALS['_GET_POST'][$key];
    } else {
      return $GLOBALS['_GET_POST'][$key];
    }

  } else if ($fillWithEmptyString === true) {
    return '';
  }

  return null;
}

/**
 * filter_var (with arrays || string)
 *
 * @param      $input
 * @param null $filter
 *
 * @return array|mixed
 */
function filter_var_maybe_array($input, $filter = null)
{
  if (is_array($input)) {
    unset($value);
    foreach ($input as $key => $value) {
      /** @noinspection AlterInForeachInspection */
      $input[$key] = filter_var_maybe_array($value, $filter);
    }
  }

  if (is_string($input)) {
    $input = filter_var($input, $filter);
  }

  return $input;
}

/**
 * Get COOKIE input
 *
 * @param string $key
 * @param mixed  $filter
 * @param bool   $fillWithEmptyString
 *
 * @return mixed
 */
function cookie($key = null, $filter = null, $fillWithEmptyString = false)
{
  if (!$key) {
    if (function_exists('filter_var_array')) {
      return $filter ? filter_var_array($_COOKIE, $filter) : $_COOKIE;
    } else {
      return $_COOKIE;
    }
  }

  if (isset($_COOKIE[$key])) {

    if (FILTER_XSS === $filter) {
      // reset the filter
      $filter = null;
      // anti-xss
      $antiXss = new AntiXSS();
      $_COOKIE[$key] = $antiXss->xss_clean($_COOKIE[$key]);
    }

    if (function_exists('filter_var')) {
      return $filter ? filter_var($_COOKIE[$key], $filter) : $_COOKIE[$key];
    } else {
      return $_COOKIE[$key];
    }

  } else if ($fillWithEmptyString === true) {
    return '';
  }

  return null;
}

/**
 * Set COOKIE input
 *
 * time can be set in seconds
 *
 * @param string $key
 * @param mixed  $value
 * @param int    $time
 */
function set_cookie($key, $value, $time = SECONDS_IN_A_HOUR)
{
  $time = (int)$time;

  if ($time <= 0) {
    $expire = 0;
  } else {
    $expire = time() + $time;
  }

  setcookie($key, $value, $expire, "/");
}

/**
 * Delete COOKIE input
 *
 * @param string $key
 */
function delete_cookie($key)
{
  setcookie($key, null, time() - SECONDS_IN_A_HOUR, "/");
  unset($_COOKIE[$key]);
}

/**
 * Get a session variable.
 *
 * @param string $key
 * @param mixed  $filter
 * @param bool   $fillWithEmptyString
 *
 * @return mixed
 */
function session($key = null, $filter = null, $fillWithEmptyString = false)
{
  if (!$key) {
    if (function_exists('filter_var_array')) {
      return $filter ? filter_var_array($_SESSION, $filter) : $_SESSION;
    } else {
      return $_SESSION;
    }
  }

  if (isset($_SESSION[$key])) {

    if (FILTER_XSS === $filter) {
      // reset the filter
      $filter = null;
      // anti-xss
      $antiXss = new AntiXSS();
      $_SESSION[$key] = $antiXss->xss_clean($_SESSION[$key]);
    }

    if (function_exists('filter_var')) {
      return $filter ? filter_var($_SESSION[$key], $filter) : $_SESSION[$key];
    } else {
      return $_SESSION[$key];
    }

  } else if ($fillWithEmptyString === true) {
    return '';
  }

  return null;
}

/**
 * Set a session variable.
 *
 * @param string $key
 * @param string $value
 * @param mixed  $filter
 *
 * @return bool
 */
function set_session($key, $value = '', $filter = null)
{
  if (isset($key)) {

    if ($filter !== null) {
      $_SESSION[$key] = filter_var($value, $filter);
    } else {
      $_SESSION[$key] = $value;
    }

    return true;
  }

  return false;
}

/**
 * return the Server-Name (via virtual-host)
 *
 * @return string
 */
function serverName()
{
  return server('SERVER_NAME');
}

/**
 * return the Host-Name (via request)
 *
 * @return string
 */
function serverHttpHost()
{
  return server('HTTP_HOST');
}

/**
 * return the Server-Port
 *
 * @return string
 */
function serverPort()
{
  return server('SERVER_PORT', FILTER_SANITIZE_NUMBER_INT);
}

/**
 * return a non-empty value, if HTTPS (SSL) is in use
 *
 * @return string
 */
function serverHttps()
{
  return server('HTTPS', FILTER_SANITIZE_STRING);
}

/**
 * return the Script-URL
 *
 * @return string
 */
function serverScriptUrl()
{
  return server('SCRIPT_URL');
}

/**
 * return the Script-Filename
 *
 * @return string
 */
function serverScriptFilename()
{
  return server('SCRIPT_FILENAME');
}

/**
 * return the Request-URI e.g.: /foo.php
 *
 * @return string
 */
function serverRequestUri()
{
  return server('REQUEST_URI');
}

/**
 * return the Http-Referer
 *
 * @return string
 */
function serverHttpReferer()
{
  return server('HTTP_REFERER');
}

/**
 * return the Query-String e.g. foo=bar&lall=<script>alert('XSS')</script>
 *
 * @return string
 */
function serverQueryString()
{
  return server('QUERY_STRING');
}

/**
 * return the Http-User-Agent e.g. IE 10<script>alert('XSS')</script>
 *
 * @return string
 */
function serverHttpUserAgent()
{
  return server('HTTP_USER_AGENT');
}

/**
 * return the Remote-IP-Address (Remote-Hostname)
 *
 * @param bool $hostname return the hostname from this IP-Address
 *
 * @return string
 */
function serverRemoteAddr($hostname = false)
{
  $return = server('REMOTE_ADDR', FILTER_SANITIZE_STRING);

  if ($hostname === true && $return) {
    $return = gethostbyaddr($return);
  }

  return $return;
}

/**
 * get a file variable (from $_FILES['image'])
 *
 * @param string $key File-Variable
 * @param mixed  $filter
 *
 * @return string empty string as fallback
 */
function files($key = null, $filter = null)
{
  if (!$key) {
    if (function_exists('filter_var_array')) {
      return $filter ? filter_var_array($_FILES, $filter) : $_FILES;
    } else {
      return $_FILES;
    }
  }

  if (isset($_FILES[$key])) {

    if (FILTER_XSS === $filter) {
      // reset the filter
      $filter = null;
      // anti-xss
      $antiXss = new AntiXSS();
      $_FILES[$key] = $antiXss->xss_clean($_FILES[$key]);
    }

    if (function_exists('filter_var')) {
      return $filter ? filter_var($_FILES[$key], $filter) : $_FILES[$key];
    } else {
      return $_FILES[$key];
    }

  } else {
    return '';
  }
}

/**
 * get a server variable (from $_SERVER)
 *
 * @param string $key Server-Variable e.g.: 'REMOTE_ADDR'
 * @param mixed  $filter
 *
 * @return string empty string as fallback
 */
function server($key = null, $filter = null)
{
  if (!$key) {
    if (function_exists('filter_var_array')) {
      return $filter ? filter_var_array($_SERVER, $filter) : $_SERVER;
    } else {
      return $_SERVER;
    }
  }

  if (isset($_SERVER[$key])) {

    if (FILTER_XSS === $filter) {
      // reset the filter
      $filter = null;
      // anti-xss
      $antiXss = new AntiXSS();
      $_SERVER[$key] = $antiXss->xss_clean($_SERVER[$key]);
    }

    if (function_exists('filter_var')) {
      return $filter ? filter_var($_SERVER[$key], $filter) : $_SERVER[$key];
    } else {
      return $_SERVER[$key];
    }

  } else {
    return '';
  }
}

/**
 * get the client IP-address
 *
 * @return mixed
 */
function get_ip_address()
{
  $ip_keys = array(
      'HTTP_CLIENT_IP',
      'HTTP_X_FORWARDED_FOR',
      'HTTP_X_FORWARDED',
      'HTTP_X_CLUSTER_CLIENT_IP',
      'HTTP_FORWARDED_FOR',
      'HTTP_FORWARDED',
      'REMOTE_ADDR',
  );

  $serverTmp = server();

  foreach ($ip_keys as $key) {
    if (array_key_exists($key, $serverTmp) === true) {
      foreach (explode(',', server($key, FILTER_SANITIZE_STRING)) as $ip) {
        $ip = trim($ip);
        if (validate_ip($ip)) {
          return $ip;
        }
      }
    }
  }

  return serverRemoteAddr();
}


/**
 * Ensures an ip address is both a valid IP and does not fall within
 * a private network range.
 *
 * @param string $ip
 *
 * @return boolean
 */
function validate_ip($ip)
{
  if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
    return false;
  } else {
    return true;
  }
}

/**
 * Get url's from an plaintext string.
 *
 * @param string $string
 *
 * @return array
 */
function getUrlsFromString($string)
{
  // init
  $hits = array();
  $urls = array();

  // regEx for e.g.: https://www.domain.de/foo.php?foobar=1&email=lars%40moelleken.org&guid=test1233312&{{foo}}#bar
  $regExUrl = '/(\[?\bhttps?:\/\/[^\s()<>]+(?:\([\w\d]+\)|[^[:punct:]\s]|\/|\}|\]))/i';

  if ($string) {
    $textTemp = $string;
    while (preg_match($regExUrl, $textTemp, $hits)) {
      $hit = $hits[0];
      $hitsTmp = preg_replace("/^\[(.*)\]$/", "$1", $hit);
      if ($hitsTmp) {
        $hit = $hitsTmp;
      }
      $textTemp = str_replace($hit, '', $textTemp);
      $urls[] = (string)$hit;
    }
  }

  return $urls;
}

/**
 * quick-debug: print the variable $var [exit] [echo || return]
 *
 * @param mixed   $var
 * @param boolean $exit
 * @param boolean $echo
 * @param boolean $plaintext
 * @param boolean $delayedMode
 *
 * @return string
 */
function dump($var, $exit = true, $echo = true, $plaintext = false, $delayedMode = false)
{
  Kint::enabled(true);

  $stash = Kint::settings();

  Kint::$cliDetection = true;
  Kint::$expandedByDefault = true;

  if ($delayedMode === true) {
    Kint::$delayedMode = true;
  }

  if ($plaintext === true) {
    Kint::enabled(Kint::MODE_WHITESPACE);
  }

  $output = Kint::dump($var);

  Kint::settings($stash);
  Kint::enabled(false);

  if ($echo === true) {
    echo $output;
  } else {
    return $output;
  }

  if ($exit) {
    exit();
  } else {
    return '';
  }
}