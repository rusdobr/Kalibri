<?php
session_start();

define('PAGE_START', microtime(true) );
define('K_APP_FOLDER', str_replace( '\\', '/', realpath('..') ).'/');

require_once( '../../Kalibri/_init.php' );

$appMode = strpos( $_SERVER['HTTP_HOST'], '.dev' ) === false? null: 'dev';

Kalibri::app( new \Kalibri\Application( K_APP_FOLDER, $appMode ) )->run();