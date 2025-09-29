<?php

ini_set('display_errors', true);

/**
 *
 */
define('DS', "/");

/**
 *
 */
define('DS_REVERSE', "\\");

/**
 *
 */
define('PATH', substr($_SERVER['SCRIPT_NAME'], 0, stripos($_SERVER['SCRIPT_NAME'], 'index')));

/**
 *
 */
define('URL', $_SERVER['REQUEST_SCHEME'] . ':' . DS . DS . $_SERVER['SERVER_NAME'] . PATH);

/**
 *
 */
define('EXAMPLE', URL . 'example' . DS);

/**
 *
 */
define('ASSETS', EXAMPLE . 'assets' . DS);
