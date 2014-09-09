<?php

namespace Kalibri {

	/**
	 * @package Kalibri
	 *
	 * @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
	 */
	class View
	{
		const VAR_SCRIPTS = 'pageScripts';
		const VAR_STYLES = 'pageStyles';
		const VAR_META = 'pageMeta';
		const VAR_TITLE = 'pageTitle';
		const VAR_CONTENT = 'pageContent';

		const DIR_FALLBACK_VIEWS = 'Kalibri/Data/Template';
		/**
		 * Assigned view name
		 * @var string
		 */
		protected $_name;

		protected $viewsDir = '/View';

//------------------------------------------------------------------------------------------------//
		public function __construct( $name = null )
		{
			$this->_name = (string) $name;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Assign param to view data
		 *
		 * @param string $key Param name
		 * @param mixed $value Param value
		 *
		 * @magic
		 */
		public function __set( $key, $value )
		{
			\Kalibri::data()->set( $key, $value );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get assigned param by name
		 *
		 * @param string $key Param name
		 *
		 * @return mixed
		 */
		public function __get( $key )
		{
			return \Kalibri::data()->get( $key );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Assign view name
		 *
		 * @param string $name View file name
		 *
		 * @return \Kalibri\View\Base
		 */
		public function &setName( $name )
		{
			$this->_name = (string) $name;
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get assigned view name
		 *
		 * @return string
		 */
		public function getName()
		{
			return $this->_name;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Assign array of vars to view data
		 *
		 * @param array $array Array of variables (key will be used as var name)
		 *
		 * @return \Kalibri\View\Base
		 */
		public function &assignArray( $array )
		{
			\Kalibri::data()->merge( $array );

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Check is view file exists in application
		 *
		 * @param string $name View name
		 *
		 * @return bool
		 */
		public function isExists( $name = null, $path = null )
		{
			return \file_exists( $this->getLocation( $name, $path ) );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get view file name location
		 *
		 * @param string $name View name
		 *
		 * @return string
		 */
		public function getLocation( $name = null, $path = null )
		{	
			$appLocation = \Kalibri::app()->getLocation().'/App';

			if( $name === null )
			{
				$name = $this->_name;
			}
			//var_dump( $this->viewsDir );
			// Search in location if specified
			if( $path !== null )
			{
				// Add trailing slash at the end
				if( $path !== '' && $path[ \strlen( $path )-1 ]!== '/' )
				{
					$path .= '/';
				}

				return $path.$this->viewsDir.'/'.$name.'.php';
			}

			// Search in application path
			if( \file_exists( $appLocation.$this->viewsDir.'/'.$name.'.php' ) )
			{
				return $appLocation.$this->viewsDir.'/'.$name.'.php';
			}

			// Try to find template in base kalibri templates
			if( \file_exists( K_ROOT.self::DIR_FALLBACK_VIEWS.'/'.$name.'.php' ) )
			{
				return K_ROOT.self::DIR_FALLBACK_VIEWS.'/'.$name.'.php';
			}

			return null;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Insert sub view into view or layout
		 *
		 * @param string $name View file name
		 */
		public function insert( $name, $asString = false )
		{
			$view = new View( $name );
			$view->render( $asString );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Render view
		 *
		 * @param bool $asString
		 *
		 * @return mixed(string,bool)
		 */
		public function render( $asString = false, $path = null )
		{
            $output = null;

			if( !empty( $this->_name ) )
			{
				if( $this->isExists( $this->_name, $path ) )
				{
					\ob_start();

					\extract( \Kalibri::data()->getData() );
					include( $location = $this->getLocation( null, $path ) );
					$output = ob_get_contents();

					K_COMPILE_ROUTES && \Kalibri::compiler()->skip( $location );

					@\ob_end_clean();
				}
				else
				{
					\Kalibri::error()->show("View with name '{$this->_name}' not found");
				}
			}

			if( !$asString )
			{
				echo $output;
			}

			\Kalibri::data()->set( self::VAR_CONTENT, $output );

			return $output;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get string representation of rendered view
		 *
		 * @magic
		 *
		 * @return string
		 */
		public function __toString()
		{
			return $this->render( true );
		}

//------------------------------------------------------------------------------------------------//
		public function setViewsDir( $path )
		{
			$this->viewsDir = $path;
			return $this;
		}
	}
}