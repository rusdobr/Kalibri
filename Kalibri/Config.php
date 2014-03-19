<?php
/**
 * Kalibri Config class file
 * 
 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
 */

namespace Kalibri {
	/**
	 * Kalibri\Config responsible for reading main configuration files. It will look for appropriate 
	 * config for current application mode (if any).

     * @version 0.4
     * @package Kalibri
     * @since 0.1
     *
	 * @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
	 */
	class Config
	{
		const MAIN_CONFIG_NAME = 'main';

		protected $_data = array();
		protected $_cache = array();

//------------------------------------------------------------------------------------------------//
		function __construct()
		{
			$this->load('_config', K_ROOT.'Kalibri/');
		}

//------------------------------------------------------------------------------------------------//
		public function getAll()
		{
			return $this->_data;
		}
		
//------------------------------------------------------------------------------------------------//
		/**
		 * @assert ("path.to.item") == null
		 * @assert ("path.to.item", true) == true
		 */
		public function get( $path, $default = null )
		{	
			if( isset( $this->_cache[ $path ] ) ) {
				return $this->_cache[ $path ];
			}

			$keys = explode( '.', $path );
			$value = $this->_data;

			foreach( $keys as $param )
			{
				if( isset( $value[ $param ] ) )
				{
					$value = $value[ $param ];
				}
				else
				{
					return $this->_cache[ $path ] = $default;
				}
			}

			return $this->_cache[ $path ] = $value;
		}

//------------------------------------------------------------------------------------------------//
		public function load( $configName = null, $configLocation = null )
		{	
			$configName = $configName ?: self::MAIN_CONFIG_NAME;
			$configLocation = $configLocation ?: \Kalibri::app()->getLocation().'App/Config/';

			$configPath = $configLocation.$configName.'.php';

			if( file_exists( $configPath ) )
			{
				$this->_data = array_merge( $this->_data, include $configPath );

				( K_COMPILE_BASE || K_COMPILE_ROUTES ) && \Kalibri::compiler()->skip( $configPath );

				return $this;
			}

			throw new \Kalibri\Exception("Config '$configName' not found");
		}
	}
}