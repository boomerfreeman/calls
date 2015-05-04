<?php

error_reporting(E_ALL);

define('TMVC_ERROR_HANDLING',1);

/* directory separator alias */
if(!defined('DS'))
  define('DS',DIRECTORY_SEPARATOR);

/* set the base directory */
if(!defined('TMVC_BASEDIR'))
  define('TMVC_BASEDIR',dirname(__FILE__) . DS . '..' . DS . 'tinymvc' . DS);

/* include main tmvc class */
require(TMVC_BASEDIR . 'sysfiles' . DS . 'TinyMVC.php');

/* instantiate */
$tmvc = new tmvc();

/* tally-ho! */
$tmvc->main();

?>
