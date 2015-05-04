<?php

/**
 * database.php
 *
 * application database configuration
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

$config['default']['plugin'] = 'TinyMVC_PDO'; // plugin for db access
$config['default']['type'] = 'mysql';      // connection type
$config['default']['host'] = 'localhost';  // db hostname
$config['default']['name'] = 'calls';     // db name
$config['default']['user'] = 'root';     // db username
$config['default']['pass'] = '';     // db password
$config['default']['persistent'] = false;  // db connection persistence?

?>