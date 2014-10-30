<?php

/**
 * @method \Kalibri\L10n l10n()
 * @method \Kalibri\Db db() 
 * @method \Kalibri\Cache\Driver\Memcache cache()
 * @method \Kalibri\Uri uri()
 * @method \Kalibri\Error error()
 * @method \Kalibri\Auth auth()
 */
final class Kalibri
{
	protected static $data = array();
	protected static $autoClasses = array();
	protected static $models = array();
	
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
		return self::$data[ $alias ] = $instance;
	}
	
//------------------------------------------------------------------------------------------------//
	public static function &get( $alias, $initClass = null )
	{
		// Try to init class before use
		if( !isset( self::$data[ $alias ] ) )
		{
			if( is_object( $initClass ) )
			{
				self::$data[ $alias ] = $initClass;
			}
			elseif( isset( self::$autoClasses[ $alias ] ) )
			{
				self::$data[ $alias ] = new self::$autoClasses[ $alias ];
			}
		}
		
		$return = isset( self::$data[ $alias ] )? self::$data[ $alias ]: null;
		
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
		return isset( self::$data[ $alias ] ) && self::$data[ $alias ] !== null;
	}
	
//------------------------------------------------------------------------------------------------//
	public static function __callStatic( $name, array $arguments )
	{
		if( count( $arguments ) )
		{
			self::$data[ $name ] = current( $arguments );
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
		self::$autoClasses = $list;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
     * @param \Kalibri\Application $app
     *
	 * @return \Kalibri\Application 
	 */
	public static function &app( \Kalibri\Application $app = null )
	{
		if( $app )
		{
			self::$data['app'] = $app;
		}
		
		$return = isset( self::$data['app'] )? self::$data['app']: self::get('app');
		
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
	/**
     * @param \Kalibri\Config $config
     *
	 * @return \Kalibri\Config
	 */
	public static function &config( \Kalibri\Config $config = null )
	{
		if( $config )
		{
			self::$data['config'] = $config;
		}
		
		$return = isset( self::$data['config'] )? self::$data['config']: self::get('config');
		
		return $return;
	}

//------------------------------------------------------------------------------------------------//
	/**
     *
     * @param \Kalibri\Controller\Base $controller
     *
	 * @return \Kalibri\Controller\Page
	 */
	public static function &controller( \Kalibri\Controller\Base $controller = null )
	{
		if( $controller )
		{
			self::$data['controller'] = $controller;
		}
		
		$return = isset( self::$data['controller'] )? self::$data['controller']: self::get('controller');
				
		return $return;
	}
	
//------------------------------------------------------------------------------------------------//
    /**
     * @param $name
     * @param Kalibri\Model\Base $class
     * @throws Exception
     *
     * @return \Kalibri\Model\ActiveEntity
     */
	public static function model( $name, \Kalibri\Model\Base $class = null )
	{
		if( $class )
		{
			return self::$models[ $name ] = $class;
		}
		
		if( !isset( self::$models[ $name ] ) )
		{
			$className = self::app()->getNamespace().'\\App\\Model\\'.ucfirst( $name );
			$kalibriClassName = '\\Kalibri\\Model\\'.\ucfirst( $name );
			
			if( class_exists( $className, true ) )
			{
				return self::$models[ $name ] = new $className;
			}
			elseif( class_exists( $kalibriClassName, true ) )
			{
				
				return self::$models[ $name ] = new $kalibriClassName;
			}
			else
			{
				throw new \Exception("Can't load model '$className'");
			}
		}
		
		return self::$models[ $name ];
	}
	
//------------------------------------------------------------------------------------------------//
	public static function tmp( $fileName = null )
	{
		return self::app()->getLocation().'Data/cache/'.$fileName;
	}
}