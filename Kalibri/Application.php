<?php

namespace Kalibri;

use \Kalibri\Utils\Compiler;
use \Kalibri\Logger\Base as Logger;

/**
 *  @package Kalibri
 *
 *  @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
 */
class Application
{
	/**
	 * Location of current application
	 * @var string
	 */
	protected $_location;
	/**
	 * Namespace where application is located. This is required to load controllers easily
	 * @var string
	 */
	protected $_namespace;

	/**
	 * Application mode string (subdir for configs)
	 * @var string
	 */
	protected $_mode;

//------------------------------------------------------------------------------------------------//
	/**
	 * Constructor
	 *
	 * @param string $location Location where application is placed
	 * @param string|null $mode Mode in which application should be runed
	 */
	public function __construct( $location, $mode = null )
	{
		$this->_location  = $location;
		$this->_namespace = basename( $location );
		$this->_mode      = $mode;
	}

//------------------------------------------------------------------------------------------------//
	public function getNamespace()
	{
		return $this->_namespace;
	}

//------------------------------------------------------------------------------------------------//
	public function getLocation()
	{
		return $this->_location;
	}

//------------------------------------------------------------------------------------------------//
	public function run()
	{
		$this->init();
		ob_start();

		\Kalibri::router()->route();

		K_COMPILE_ROUTES && \Kalibri::compiler()->includeCached(Compiler::NAME_ROUTE);

		\Kalibri::router()->run();

		$this->shutdown();
	}

//------------------------------------------------------------------------------------------------//
	public function shutdown()
	{
		if( \Kalibri::is('event') )
		{
			\Kalibri::event()->trigger('app.shutdown');
		}

		if( \Kalibri::config()->get('debug.log.is-enabled', false) )
		{
			\Kalibri::logger()->add(Logger::L_DEBUG, 'shutdown', $this);
			\Kalibri::logger()->write();
		}

		K_COMPILE_ROUTES && \Kalibri::compiler()->compile(Compiler::NAME_ROUTE);
		exit();
	}

//------------------------------------------------------------------------------------------------//
	protected function init()
	{
		K_COMPILE_ROUTES && \Kalibri::compiler()->compile(Compiler::NAME_BASE);
		\Kalibri::config()->load();

		if( $this->_mode )
		{
			try
			{
				\Kalibri::config()->load( $this->_mode );
			}
			catch( \Exception $e ){}
		}

		// Set list of classes that will be auto inited on use
		\Kalibri::setAutoInitClasses( \Kalibri::config()->get('init.classes') );
		//\Kalibri::logger()->init( \Kalibri::config()->get('debug.log') );
		\Kalibri::router()->setSegments( \Kalibri::uri()->getSegments() );

		if(session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }

		if( \Kalibri::config()->get('debug.log.is-enabled', false) )
		{
			\Kalibri::logger()->add(\Kalibri\Logger\Base::L_DEBUG, 'init', $this);
		}

		ob_start();
	}
}
