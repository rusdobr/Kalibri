<?php

/**
 * @method \Kalibri\Db db() 
 * @method \Kalibri\Cache cache()
 */
final class Kalibri
{
	protected static $_data = array();
	protected static $_autoClasses = array();
	protected static $_models = array();
	
//------------------------------------------------------------------------------------------------//
	/**
	 * Set value
	 * 
	 * @param string $alias
	 * @param mixed $instance
	 * 
	 * @return mixed
	 */
	public static function set( $alias, $instance )
	{
		return self::$_data[ $alias ] = $instance;
	}
	
//------------------------------------------------------------------------------------------------//
	public static function &get( $alias, $initClass = null )
	{
		// Try to init class before use
		if( !isset( self::$_data[ $alias ] ) )
		{
			if( is_object( $initClass ) )
			{
				self::$_data[ $alias ] = $initClass;
			}
			elseif( isset( self::$_autoClasses[ $alias ] ) )
			{
				self::$_data[ $alias ] = new self::$_autoClasses[ $alias ];
			}
		}
		
		$return = isset( self::$_data[ $alias ] )? self::$_data[ $alias ]: null; 
		
		return $return;
	}

//------------------------------------------------------------------------------------------------//
	/**
	 * Check is specified instance created
	 *
	 * @param string $alias Aliased name
	 *
	 * @return bool
	 */
	public static function is( $alias )
	{
		return isset( self::$_data[ $alias ] ) && self::$_data[ $alias ] !== null;
	}
	
//------------------------------------------------------------------------------------------------//
	public static function __callStatic( $name, array $arguments )
	{
		if( count( $arguments ) )
		{
			self::$_data[ $name ] = current( $arguments );
		}
		
		return self::get( $name );
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * Set list of classes that will be auto inited on first use
	 * 
	 * @param array $list
	 * 
	 * @return null
	 */
	public static function setAutoInitClasses( $list )
	{
		self::$_autoClasses = $list;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * @return \Kalibri\Application 
	 */
	public static function &app( \Kalibri\Application $app = null )
	{
		if( $app )
		{
			self::$_data['app'] = $app;
		}
		
		$return = isset( self::$_data['app'] )? self::$_data['app']: self::get('app');
		
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
     * Get instance of autoloader
     *
     * @param \Kalibri\Autoload $autoload
     *
	 * @return \Kalibri\Autoload
	 */
	public static function &autoload( \Kalibri\Autoload $autoload = null )
	{
		if( $autoload )
		{
			self::$_data['autoload'] = $autoload;
		}

		$return = isset( self::$_data['autoload'] )? self::$_data['autoload']: self::get('autoload');
     	return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * @return \Kalibri\Config
	 */
	public static function &config( \Kalibri\Config $app = null )
	{
		if( $app )
		{
			self::$_data['config'] = $app;
		}
		
		$return = isset( self::$_data['config'] )? self::$_data['config']: self::get('config'); 
		
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * @return \Kalibri\Error
	 */
	public static function &error( \Kalibri\Error $app = null )
	{
		if( $app )
		{
			self::$_data['error'] = $app;
		}
		
		$return = isset( self::$_data['error'] )? self::$_data['error']: self::get('error'); 
		
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * @return \Kalibri\Output
	 */
	public static function &output( \Kalibri\Output $app = null )
	{
		if( $app )
		{
			self::$_data['output'] = $app;
		}
		
		$return = isset( self::$_data['output'] )? self::$_data['output']: self::get('output'); 
		
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * @return \Kalibri\Uri
	 */
	public static function &uri( \Kalibri\Uri $app = null )
	{
		if( $app )
		{
			self::$_data['uri'] = $app;
		}
		
		$return = isset( self::$_data['uri'] )? self::$_data['uri']: self::get('uri');
		
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * @return \Kalibri\Router
	 */
	public static function &router( \Kalibri\Router $app = null )
	{
		if( $app )
		{
			self::$_data['router'] = $app;
		}
		
		$return = isset( self::$_data['router'] )? self::$_data['router']: self::get('router'); 
		
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * @return \Kalibri\Logger\Base
	 */
	public static function &logger( \Kalibri\Logger\Base $app = null )
	{
		if( $app )
		{
			self::$_data['logger'] = $app;
		}
		
		$return = isset( self::$_data['logger'] )? self::$_data['logger']: self::get('logger');
		
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * @return \Kalibri\Benchmark
	 */
	public static function &benchmark( \Kalibri\Benchmark $obj = null )
	{
		if( $obj )
		{
			self::$_data['benchmark'] = $obj;
		}
		
		$return = isset( self::$_data['benchmark'] )? self::$_data['benchmark']: self::get('benchmark');
		
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * @return \Kalibri\Event
	 */
	public static function &event( \Kalibri\Event $app = null )
	{
		if( $app )
		{
			self::$_data['event'] = $app;
		}
		
		$return = isset( self::$_data['event'] )? self::$_data['event']: self::get('event');
		
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * @return \Kalibri\View\Data
	 */
	public static function &data( \Kalibri\View\Data $app = null )
	{
		if( $app )
		{
			self::$_data['data'] = $app;
		}
		
		$return = isset( self::$_data['data'] )? self::$_data['data']: self::get('data');
		
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * @return \Kalibri\Controller\Page
	 */
	public static function &controller( \Kalibri\Controller\Base $app = null )
	{
		if( $app )
		{
			self::$_data['controller'] = $app;
		}
		
		$return = isset( self::$_data['controller'] )? self::$_data['controller']: self::get('controller');
				
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * @param \Kalibri\Auth $instance
	 * 
	 * @return \Kalibri\Auth
	 */
	public static function &auth( \Kalibri\Auth $instance = null )
	{
		if( $instance )
		{
			self::$_data['auth'] = $instance;
		}
		
		$return = isset( self::$_data['auth'] )? self::$_data['auth']: self::get('auth');
				
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
	 * @param string $instance
	 * @param \Kalibri\Model\Base
	 * 
	 * @return \Kalibri\Model\Active
	 */
	public static function model( $name, \Kalibri\Model\Base $class = null )
	{
		if( $class )
		{
			return self::$_models[ $name ] = $class;
		}
		
		if( !isset( self::$_models[ $name ] ) )
		{
			$className = self::app()->getNamespace().'\\App\\Model\\'.ucfirst( $name );
			$kalibriClassName = '\\Kalibri\\Model\\'.\ucfirst( $name );
			
			if( class_exists( $className, true ) )
			{
				return self::$_models[ $name ] = new $className;
			}
			elseif( class_exists( $kalibriClassName, true ) )
			{
				
				return self::$_models[ $name ] = new $kalibriClassName;
			}
			else
			{
				throw new \Exception("Can't load model '$className'");
			}
		}
		
		return self::$_models[ $name ];
	}
	
//------------------------------------------------------------------------------------------------//
	public static function tmp( $fileName = null )
	{
		return self::app()->getLocation().'Data/cache/'.$fileName;
	}
}