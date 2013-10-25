<?php
/**
 * Kalibri Router class file
 * 
 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
 */

namespace Kalibri {

	/**
	 * Router class process HTTP requests and call appropriate controller and his action.
	 * Also checked routing maps to re-map requests. 
	 * 
	 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
	 * @version 0.4
	 * @package Kalibri
	 * @since 0.1
	 */
	class Router
	{	
		/**
		 * Url segments that points to current dir, controller, action
		 * @var array
		 */
		protected $_segments = array();

		/**
		 * Params that will be passed to controller action
		 * @var array
		 */
		protected $_params = array();

		/**
		 * Controller to call
		 * @var string
		 */
		protected $_controller;

		/**
		 * Directory where controller stored
		 * @var string
		 */
		protected $_dir;

		/**
		 * Controller action to call
		 * @var string
		 */
		protected $_action;
		protected $_baseNamespace;
		protected $_baseSegment;

		protected $_options;

//------------------------------------------------------------------------------------------------//
		public function __construct()
		{
			// Load routing options and set default route
			$this->_options = \Kalibri::config()->get('route');
			$this->_controller = $this->_options['default']['controller'];
			$this->_action = $this->_options['default']['action'];
			$this->_baseNamespace = \Kalibri::app()->getNamespace();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Method can re-define url segments for re-maping request to another controller and action
		 * 
		 * @param array $segments
		 * 
		 * @return \Kalibri\Router
		 */
		public function &setSegments( array $segments )
		{
			$this->_segments = $segments;		
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get array with current segmented path.
		 * 
		 * @return array
		 */
		public function getSegments()
		{
			return $this->_segments;
		}

//------------------------------------------------------------------------------------------------//
		public function getBaseSegment()
		{
			return $this->_baseSegment;
		}

//------------------------------------------------------------------------------------------------//
		public function getBaseNamespace()
		{
			return $this->_baseNamespace;
		}

//------------------------------------------------------------------------------------------------//
		public function getDirectory()
		{
			return $this->_dir;
		}

//------------------------------------------------------------------------------------------------//
		public function getController()
		{
			return $this->_controller;
		}

//------------------------------------------------------------------------------------------------//
		public function getAction()
		{
			return $this->_action;
		}

//------------------------------------------------------------------------------------------------//
		public function route( array $segments = null )
		{
			if( $segments !== null )
			{
				$this->_segments = $segments;
			}

			if( empty( $this->_segments ) )
			{
				return $this;
			}

			//if( $this->checkRouteMap() )
			//{
			//	return $this;
			//}

			$this->_baseNamespace = $this->checkProjectBase();

			$basePath = K_ROOT.$this->_baseNamespace.'/App/Controller/';
			$upper = '';
			$segmentsCount = count( $this->_segments );

			for( $i=0; $i< $segmentsCount; $i++ )
			{
				if( $this->_segments[ $i ] !== '' && preg_match('/^[\w\d_]+$/', $this->_segments[ $i ] ) )
				{
					$upper = \ucfirst( $this->_segments[ $i ] );

					if( \is_dir( $basePath.$this->_dir.$upper ) )
					{
						$this->_dir .= $upper.'/';
						continue;
					}
					elseif( \file_exists( $basePath.$this->_dir.$upper.'.php' ) )
					{
						$this->_controller = $upper;

						if( isset( $this->_segments[ ++$i ] ) && !empty( $this->_segments[ $i ] ) )
						{
							$this->_action = \strtolower( $this->_segments[ $i ] );

							if( isset( $this->_segments[ ++$i ] ) )
							{
								$this->_params = \array_slice( $this->_segments, $i );
								return $this;
							}
						}
					}
					else
					{
						\Kalibri::error()->show404();
					}
				}
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		public function checkRouteMap()
		{
			//\Kalibri::event()->triggerByName('checkMap');

			return false;
		}

//------------------------------------------------------------------------------------------------//
		public function checkProjectBase()
		{
			$map = \Kalibri::config()->get('route.map-project');

			if( $map )
			{
				foreach( $map as $way=>$name )
				{
					if( $way == $this->_segments[0] )
					{
						// Remove first segment that contains map key
						array_shift( $this->_segments );
						return $name;
					}
				}
			}

			return \Kalibri::app()->getNamespace();
		}

//------------------------------------------------------------------------------------------------//
		public function internalRedirect( $uri )
		{
			$this->route( \Kalibri::uri()->setUri( $uri )->getSegments() )->run();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Execute controller and action
		 */
		public function run()
		{
			try
			{
				$controllerName = "\\{$this->_baseNamespace}\\App\\Controller\\"
					.str_replace( '/', '\\', $this->_dir.$this->_controller );

				if( \class_exists( $controllerName ) )
				{
					$actionName = $this->_action.$this->_options['action-prefix'];

					//\Kalibri::logger()->add( Logger::L_DEBUG, 'Use controller: '.$controllerPath);

					$controller = new $controllerName();

					// Set global controller instance
					\Kalibri::controller( $controller );

					//If exists method '_remap' call it and pass method name and params
					if( \method_exists( $controller, '_remap' ) )
					{
						\call_user_func_array(
								array( $controller, '_remap' ),
								array( $actionName, $this->_params )
						);
					}
					//If Method exists call it and pass params
					elseif( \is_callable("{$controllerName}::{$actionName}") )
					{
						\call_user_func_array(
							array( $controller, $actionName ),
							$this->_params
						);
					}
					else
					{
						throw new \Kalibri\Exception\Page\NotFound();
					}

					// Check is controller already rendered by hand, if not render it
					if( !$controller->isRendered() )
					{
						\call_user_func_array( array( $controller, '_render'), array() );
					}

					return $this;

				}
			}
			catch( \Kalibri\Exception\Page\NotFound $e )
			{
				\Kalibri::error()->show404();
			}
			catch( \Exception $e )
			{
				//Exception in page controller
				\Kalibri::error()->showException( $e );
			}

			\Kalibri::error()->show404();
		}
	}
}