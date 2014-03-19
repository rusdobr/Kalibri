<?php

namespace Kalibri {

    /**
     *  @package Kalibri
     *
     *  @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
     */
	class Autoload
	{
		protected $_autoloaded = array();
		protected $_appName;

//------------------------------------------------------------------------------------------------//
		public function __construct()
		{
			$this->_appName = \Kalibri::app()->getNamespace();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Autoloading classes from library.
		 * Can load classes with folder name as prefix or classes without prefix in application
		 * classes folder.
		 *
		 * So you can name app clases as Default_Class_Name or Class_Name.
		 * Note: In application sources you can use ONLY declared in class def. name.
		 *
		 * @param string $className
		 *
		 * @return bool
		 */
		public function library( $className )
		{	
			$path = str_replace( '\\', '/', $className ).'.php';

			if( @include_once( $path ) )
			{
				return true;
			}

			// Not loaded eat, try to load helper
			return $this->helper( $className );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Load helper
		 * 
		 * @param string $className
		 * 
		 * @return bool
		 */
		public function helper( $className )
		{
			$helperName = null;
			$baseClassName = substr( $className, strrpos( $className, '\\' ) );

			if( @include_once( $this->_appName.'/Helper/'.$baseClassName.'.php' ) )
			{
				$helperName = $this->_appName.'\\Helper\\'.$baseClassName;
			}

			// Try to load kalibri helper as fallback option
			if( $helperName == null && @include_once('Kalibri/Helper/'.$baseClassName.'.php') )
			{
				$helperName = '\\Kalibri\\Helper\\'.$baseClassName;
			}

			// Init helper in case it's loaded
			if( !empty( $helperName ) )
			{
				// Execute helper initialization
				$helperName::init();

				// Create alias for not prefixed helper class in global namespace
				class_alias( $helperName, $baseClassName );

				return true;
			}

			return false;
		}
	}
}