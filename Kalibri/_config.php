<?php

return array(
	'base'=>'',
	'entry'=>'',
	'base-host'=>'',
	'permitted-uri-chars'=>'\w \d~%\.:_\-\?\=&',
	'response'=>array(
		'default'=>array(
			'content-type'=>'text/html',
			'charset'=>'utf-8'
		)
	),
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
			'event'   => '\\Kalibri\\Event',
			'router'  => '\\Kalibri\\Router',
			'auth'    => '\\Kalibri\\Auth',
			'db'      => '\\Kalibri\\Db',
			'l10n'    => '\\Kalibri\\L10n',
			'cache'   => '\\Kalibri\\Cache\\Driver\\Local',
			'autoload'=> '\\Kalibri\\Autoload',
			'compiler'=> '\\Kalibri\\Utils\\Compiler',
			'benchmark'=>'\\Kalibri\\Benchmark'
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
		'collect-db-queries'=>false,
		'allow-benchmark'=>false,
		'log'=>array(
			'is-enabled'=>false,
			'driver'=>'\Kalibri\Logger\Driver\File',
			'path'=>'../Logs/',
			'rotate'=>false,
			'date-format'=>'Y-m-d H:i:s',
			'format'=>"[%date] %uniq %level %class: %msg\n"
		),
		'show'=>array(
			'errors'=>false,
			'panel'=>false
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
	)
);