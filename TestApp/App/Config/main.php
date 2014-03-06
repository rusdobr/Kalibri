<?php

return array(
	'base'=>'http://test.dev/Public/',
	'entry'=>'?',
	'base-host'=>'test.dev',
	'permitted-uri-chars'=>'\w \d~%\.:_\-\?\=&',
	'response'=>array(
		'default'=>array(
			'content-type'=>'text/html',
			'charset'=>'utf-8'
		)
	),
	'compile-config'=>false,
	'l10n'=>array(
		'language'=>'ru',/*required*/
		'languages'=>array(
			'ru'=>'Русский',
			'en'=>'English'
		),
		'is-allowed'=>true /*required*/
	),
	'page'=>array(
		'title'=>array(
			'prefix'=>'',
			'suffix'=>'',
			'default'=>'Default'
		),
        'after-login'=>'/',
        'home'=>'/'
	),
	'auth'=>array(
		'salt'=>'sdD&4G!-*@gfS',
		'profile'=>'\\Kalibri\\Auth\\Profile',
		'login-field'=>'email',
		'min-password-length'=>4
	),
	'route'=>array(
		'fetch-url-from'=>'clear-query',/*passible values: get, query*/
		'action-prefix'=>'',
		'default'=>array(
			'controller'=>'Home',
			'action'=>'index',
		)
	),
	'resources'=>array(
		'images'=>'img/',
		'js'=>'js/',
		'styles'=>'css/'
	),
	'init'=>array(
		'classes'=>array(
			'error'   => '\\Kalibri\\Error',
			'data'    => '\\Kalibri\\View\\Data',
			'uri'     => '\\Kalibri\\Uri',
			'logger'  => '\\Kalibri\\Logger\\Driver\\File',
			'event'   => '\\Kalibri\\Event\\Dispatcher',
			'router'  => '\\Kalibri\\Router',
			'auth'    => '\\Kalibri\\Auth',
			'db'      => '\\Kalibri\\Db',
			'l10n'    => '\\Kalibri\\L10n',
			'cache'   => '\\Kalibri\\Cache\\Driver\\Local',
			'autoload'=> '\\Kalibri\\Autoload',
			'compiler'=> '\\Kalibri\\Utils\\Compiler'
		)
	),
	'error'=>array( 
		'view'=>array(
			'404'=>'Error/404',
			'403'=>'Error/403',
			'500'=>'Error/500',
			'exception'=>'Error/exception'
		)
	),
	'debug'=>array(
		'collect-db-queries'=>true,
		'allow-benchmark'=>true,
		'log'=>array(
			'is-enabled'=>true,
			'driver'=>'\Kalibri\Logger\Driver\File',
			'path'=>'../Logs/',
			'rotate'=>false,
			'date-format'=>'Y-m-d H:i:s',
			'format'=>"[%date] %uniq %level %class: %msg\n"
		),
		'show'=>array(
			'errors'=>true,
			'panel'=>true
		)
	),
	'cache'=>array(
		'is-enabled'=>false,
		'servers' => array(
			array( 
				'host'=>'127.0.0.1',
				'port'=>11211
			)
		)
	),
	'db'=>array(
		'default'=>'default',
		'connection'=>array(
			'default'=>array(
				'dsn'=>'mysql:unix_socket=/run/mysqld/mysqld.sock;dbname=test',
				'driver'=>'mysql',
				'user'=>'root',
				'password'=>'123',
				'table-prefix'=>'',
				'encoding'=>'utf8'
			)
		)
	)
);