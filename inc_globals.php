<?php

// disable register globals
/** @noinspection DeprecatedIniOptionsInspection */
use voku\db\DB;
use voku\helper\AntiXSS;
use webdev\ParisWrapper;

if (isset($_REQUEST) && ini_get("register_globals")) {
  foreach ($_REQUEST as $k => $v) {
    unset($GLOBALS[$k]);
  }
}

// start the time measurement
$GLOBALS['startTimerGlobal'] = microtime();

// #############################################################################
// # set global-config (for the cms & php)
// #############################################################################

// version
/*
 * [1] a number (major - breaking change in the api)
 * [2] a period
 * [3] a number (minor - extended the api)
 * [4] a period
 * [5] a number (patch - no changes in the api)
 * ...
 * [6] OPTIONAL: a hyphen, followed by a number (build)
 * [7] OPTIONAL: a collection of pretty much any non-whitespace characters (tag)
 */
$GLOBALS['webdeb_news_version'] = '0.0.1';

// charset
$GLOBALS['webdeb_news_charset'] = 'UTF-8';

// do not display php errors / warnings by default
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// set charset
ini_set('default_charset', $GLOBALS['webdeb_news_charset']);

// strip invalid characters from mbstrings
ini_set('mbstring.substitute_character', "none");

// iconv encoding default
iconv_set_encoding("internal_encoding", $GLOBALS['webdeb_news_charset']);

// Multibyte encoding
mb_internal_encoding($GLOBALS['webdeb_news_charset']);
mb_http_output($GLOBALS['webdeb_news_charset']);

// don't show SimpleXML/DOM errors (most of the web is invalid)
libxml_use_internal_errors(true);

// will be replaced in the "Language"-Class
date_default_timezone_set('Europe/Berlin');
setlocale(LC_ALL, 'de.' . $GLOBALS['webdeb_news_charset']);

// #############################################################################
// # define constant | making code more verbose
// #############################################################################

if (!defined('SECONDS_IN_A_MINUTE')) {
  define('SECONDS_IN_A_MINUTE', 60);
}
if (!defined('SECONDS_IN_A_HOUR')) {
  define('SECONDS_IN_A_HOUR', 3600);
}
if (!defined('SECONDS_IN_A_DAY')) {
  define('SECONDS_IN_A_DAY', 86400);
}
if (!defined('SECONDS_IN_A_WEEK')) {
  define('SECONDS_IN_A_WEEK', 604800);
}
if (!defined('SECONDS_IN_A_MONTH')) {
  define('SECONDS_IN_A_MONTH', 2592000);
}
if (!defined('SECONDS_IN_A_YEAR')) {
  define('SECONDS_IN_A_YEAR', 31536000);
}
if (!defined('FILTER_XSS')) {
  define('FILTER_XSS', 464464);
}

// fallback for "__DIR__" | please upgrade your php-version!!!
if (!defined('__DIR__')) {
  /** @noinspection dirnameCallOnFileConstantInspection */
  define('__DIR__', dirname(__FILE__));
}

// fallback for "DOCUMENT_ROOT"
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
  $_SERVER['DOCUMENT_ROOT'] = __DIR__;
}

// #############################################################################
// # include libraries
// #############################################################################

require_once __DIR__ . '/vendor/composer/autoload.php';

// #############################################################################
// # include functions
// #############################################################################

require_once __DIR__ . '/inc_functions.php';

// #############################################################################
// # DB
// #############################################################################

$dbSettings = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'webdev',
    'port'     => 3306,
    'charset'  => 'utf8mb4',

);

DB::getInstance(
    $dbSettings['hostname'],        // hostname
    $dbSettings['username'],        // username
    $dbSettings['password'],        // password
    $dbSettings['database'],        // database
    $dbSettings['port'],            // port
    $dbSettings['charset'],         // charset
    true,                           // exit_on_error
    true                            // echo_on_error
);

ParisWrapper::getInstance(
    $dbSettings['hostname'],        // hostname
    $dbSettings['username'],        // username
    $dbSettings['password'],        // password
    $dbSettings['database'],        // database
    $dbSettings['port'],            // port
    $dbSettings['charset'],         // charset
    'mysql'                         // driver
);

unset($dbSettings);

// #############################################################################
// # Session
// #############################################################################

// Prevent session-fixation
//
// See: http://en.wikipedia.org/wiki/Session_fixation
ini_set("session.cookie_httponly", 1);
ini_set("session.use_only_cookies", 1);

// Use the SHA-1 hashing algorithm
ini_set("session.hash_function", 1);

// Increase character-range of the session ID to help prevent brute-force attacks
ini_set("session.hash_bits_per_character", 6);

/** @noinspection PhpUsageOfSilenceOperatorInspection */
@session_start();

// #############################################################################
// # clear _POST && _GET
// #############################################################################

$antiXss = new AntiXSS();

clearXss($_POST, $antiXss);
clearXss($_GET, $antiXss);
clearXss($_REQUEST, $antiXss);
clearXss($_SERVER, $antiXss);
clearXss($_COOKIE, $antiXss);
clearXss($_SESSION, $antiXss);

// free memory
$antiXss = null;
