<?php

date_default_timezone_set('Europe/Kiev');

define('K_TIME', $_SERVER['REQUEST_TIME'] );
define('K_DATE', date('Y-m-d', K_TIME ) );
define('K_DATETIME', date('Y-m-d H:i:s', K_TIME) );

!defined('K_ROOT') && define('K_ROOT', str_replace( '\\', '/', realpath( dirname(__FILE__).'/../' ) ).'/' );

// Add app and kalibri to include path
set_include_path( str_replace('\\', '/', realpath(K_APP_FOLDER.'../')).PATH_SEPARATOR . K_ROOT.PATH_SEPARATOR . get_include_path());

require_once('Kalibri/Kalibri.php');

!defined('K_COMPILE_BASE') && define('K_COMPILE_BASE', false);
!defined('K_COMPILE_ROUTES') && define('K_COMPILE_ROUTES', false);

if( K_COMPILE_BASE || K_COMPILE_ROUTES )
{
	require_once('Kalibri/Utils/Compiler.php');
	\Kalibri::compiler( new \Kalibri\Utils\Compiler );
}

if( !K_COMPILE_BASE || !\Kalibri::compiler()->includeCached( \Kalibri\Utils\Compiler::NAME_BASE ) )
{
	require_once('Kalibri/Autoload.php');
	require_once('Kalibri/Application.php');
	require_once('Kalibri/Config.php');
	require_once('Kalibri/Controller/Base.php');
	require_once('Kalibri/Uri.php');
	require_once('Kalibri/Router.php');
	require_once('Kalibri/Benchmark.php');
}

// Register autoloader
spl_autoload_register( function( $className ) {
 	if( @include_once( str_replace( '\\', '/', $className ).'.php' ) )
	{
		return true;
	}

	// Not loaded eat, try to load helper
	return \Kalibri::autoload()->helper( $className );
});

\Kalibri::config( new \Kalibri\Config() );
\Kalibri::benchmark( new \Kalibri\Benchmark() )->start('kalibri-total');

function tr( $key, array $params = null )
{
	return \Kalibri::l10n()->tr( $key, $params );
}

function url( $path, $subdomain = null )
{
	return \Kalibri\Helper\Url::site( $path, $subdomain );
}

function k_ob_get_end( $flush = false )
{
	$output = ob_get_clean();
	ob_end_clean();
	
	if( $flush )
	{
		echo $output;
	}
	
	return $output;
}