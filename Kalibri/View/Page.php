<?php

namespace Kalibri\View {

	/**
	 * Main page class. Used to make abstraction of html documented generated to end user.
	 * Allow managing styles, scripts, meta data, content, etc ...
	 *
	 * @package Kalibri
	 * @subpackage View
	 *
	 * @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
	 */
	class Page
	{
		/**
		 * @var \Kalibri\View
		 */
		protected $_view;

		/**
		 * @var \Kalibri\View\Layout
		 */
		protected $_layout;

		/**
		 * @var string
		 */
		protected $_contentType = 'text/html; charset=utf-8;';

		/**
		 * @var array
		 */
		protected $_pageTitleConfig = [];

		protected $_alternativeViewLocation;

//------------------------------------------------------------------------------------------------//
		public function __construct()
		{
			// Init title data
			$this->_pageTitleConfig = \Kalibri::config()->get('page.title');

			$data = \Kalibri::data();

			// Init page data
			$data->merge( [
       /* Set default content */
       \Kalibri\View::VAR_CONTENT => $data->get( \Kalibri\View::VAR_CONTENT, ''),
       /* Set constructed page title */
       \Kalibri\View::VAR_TITLE =>   $data->get( \Kalibri\View::VAR_TITLE, $this->_pageTitleConfig['default'] ),
       /* Empty meta tags */
       \Kalibri\View::VAR_META =>    $data->get( \Kalibri\View::VAR_META, [] ),
       /* Empty scripts list */
       \Kalibri\View::VAR_SCRIPTS => $data->get( \Kalibri\View::VAR_SCRIPTS, [] ),
       /* Empty styles list */
       \Kalibri\View::VAR_STYLES =>  $data->get( \Kalibri\View::VAR_STYLES, [] ),
   ]);

			$this->setMetaContentType( $this->_contentType );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 *  Set additional views folder. Page can hold only one primary and one additional view location
         *
         * @param string $path
         *
         * @return \Kalibri\View\Page
         */
        public function setAlternativeViewLocation( $path )
		{
			$this->_alternativeViewLocation = $path;
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Set view data var value
		 *
		 * @param string $name Var name
		 * @param mixed $value Var value
		 *
		 * @magic
		 */
		public function __set( $name, mixed $value )
		{
			\Kalibri::data()->set( $name, $value );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get view data var by name
		 * @magic
		 *
		 * @param string $name Var name
		 *
		 * @return mixed
		 */
		public function __get( $name )
		{
			return \Kalibri::data()->get( $name );
		}

//------------------------------------------------------------------------------------------------//
		/**
         * Map list of vars to view. This is short alias to \Kalibri::data()->merge( $array )
         *
		 * @param array $array List of variables that should be mapped to view
		 * @return \Kalibri\View\Page
		 */
		public function &assignArray( $array )
		{
			\Kalibri::data()->merge( $array );
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Set page title
		 * 
		 * @param string $title
		 * 
		 * @return \Kalibri\View\Page
		 */
		public function &setTitle( $title )
		{
			if( $title == '' )
			{
				// Use default page title for empty titles
				$title = $this->_pageTitleConfig['default'];
			}

			\Kalibri::data()->set( \Kalibri\View::VAR_TITLE, $title );
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get page title
		 *
		 * @param bool $withPrefix Add prefix to the title
		 * @param bool $withSuffix Add suffix to the title
		 *
		 * @return string
		 */
		public function getTitle( $withPrefix = false, $withSuffix = false )
		{
			return ($withPrefix? $this->_pageTitleConfig['prefix']:'')
					.\Kalibri::data()->get( \Kalibri\View::VAR_TITLE )
					.($withSuffix? $this->_pageTitleConfig['suffix']:'');
		}

//------------------------------------------------------------------------------------------------//
		/**
         * Set content for current action. If provided will overlap view
         *
         * @param string $content
         *
		 * @return \Kalibri\View\Page
		 */
		public function &setContent( $content )
		{
			\Kalibri::data()->set( \Kalibri\View::VAR_CONTENT, $content);
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get page content
		 *
		 * @return string
		 */
		public function getContent()
		{
			return \Kalibri::data()->get( \Kalibri\View::VAR_CONTENT );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Append custom text content to the page
		 *
		 * @param string $content Content to append
		 *
		 * @return \Kalibri\View\Page
		 */
		public function &appendContent( $content )
		{
			\Kalibri::data()->set( \Kalibri\View::VAR_CONTENT, \Kalibri::data()->get( \Kalibri\View::VAR_CONTENT ).$content);
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Set page title prefix
		 *
		 * @param string $prefix New title prefix
		 *
		 * @return \Kalibri\View\Page
		 */
		public function &setTitlePrefix( $prefix )
		{
			$this->_pageTitleConfig['prefix'] = (string) $prefix;
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Set page title suffix
		 *
		 * @param string $suffix New title suffix
		 *
		 * @return \Kalibri\View\Page
		 */
		public function &setTitleSuffix( $suffix )
		{
			$this->_pageTitleConfig['suffix'] = (string) $suffix;
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Set default page title
		 * This title will be used if no title was provided.
		 *
		 * @param string $default New default title
		 *
		 * @return \Kalibri\View\Page
		 */
		public function &setDefaultTitle( $default )
		{
			$this->_pageTitleConfig['default'] = (string) $default;
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @param string $name
		 * 
		 * @return \Kalibri\View\Page
		 */
		public function &setLayoutName( $name )
		{
			if( $this->_layout instanceof \Kalibri\View\Layout )
			{
				// If layout instance already created or passed just set new name for it
				$this->_layout->setName( $name );
			}
			else
			{
				// Create new layout instance
				$this->_layout = new \Kalibri\View\Layout( $name );
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
  /**
   * @return \Kalibri\View\Page
   */
  public function &setLayout( \Kalibri\View\Layout $layout )
		{
			$this->_layout = $layout;
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		public function &getLayoutName()
		{
			return $this->_layout? $this->_layout->getName(): null;
		}

//------------------------------------------------------------------------------------------------//
		/**
         * Get current page layout
         *
		 * @return \Kalibri\View\Layout
		 */
		public function &getLayout()
		{
			return $this->_layout;
		}

//------------------------------------------------------------------------------------------------//
		/**
         * Init view with given name
         *
         * @param string $name View name
         *
		 * @return \Kalibri\View\Page
		 */
		public function &setViewName( $name )
		{
			if( $this->_view instanceof \Kalibri\View )
			{
				$this->_view->setName( $name );
			}
			else
			{
				$this->_view = new \Kalibri\View( $name );
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 *  Get current view name or null
         *
         *  @return string|null
         */
        public function getViewName()
		{
			return $this->_view instanceof \Kalibri\View? $this->_view->getName(): null; 
		}

//------------------------------------------------------------------------------------------------//
  /**
   * Set current view
   *
   *
   * @return \Kalibri\View\Page
   */
  public function &setView( \Kalibri\View $view )
		{
			$this->_view = $view;

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		public function &getView()
		{
			if( !( $this->_view instanceof \Kalibri\View ) )
			{
				$this->_view = new \Kalibri\View();
			}

			return $this->_view;
		}

//------------------------------------------------------------------------------------------------//
		/**
         * @param string $name
         * @param string $content
         *
		 * @return \Kalibri\View\Page
		 */
		public function &setMeta( $name, $content )
		{
			$this->addResource(
					'<meta name="'.$name.'" content="'.$content.'"/>',
					\Kalibri\View::VAR_META,
					$name
			);

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		public function getMeta( $name = null, $default = null )
		{
			if( !$name )
			{
				return \Kalibri::data()->get( \Kalibri\View::VAR_META );
			}

			$meta = \Kalibri::data()->get( \Kalibri\View::VAR_META );

			return $meta[ $name ] ?? $default;
		}

//------------------------------------------------------------------------------------------------//
		/**
         * @param string $value
         *
		 * @return \Kalibri\View\Page
		 */
		public function &setMetaContentType( $value )
		{
			$this->_contentType = $value;
			$this->setMeta('Content-Type', $value );

			return $this;
		}

//------------------------------------------------------------------------------------------------//
        /**
         * @param string $value
         *
         * @return \Kalibri\View\Page
         */
		public function &setMetaContentLanguage( $value )
		{
			$this->setMeta('Content-Language', $value );
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
         * @param string $value
         *
		 * @return \Kalibri\View\Page
		 */
		public function &setMetaRefresh( $value )
		{
			$this->setMeta('Refresh', $value );
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
         * @param string @value
         *
		 * @return \Kalibri\View\Page
		 */
		public function &setMetaExpires( $value )
		{
			$this->setMeta('Expires', $value );
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
         * @param string $value
         *
		 * @return \Kalibri\View\Page
		 */
		public function &setMetaCacheControl( $value )
		{
			$this->setMeta('Cache-Control', $value );
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
         * @param string @value
         *
		 * @return \Kalibri\View\Page
		 */
		public function &setMetaDescription( $value )
		{
			$this->setMeta('Description', $value);
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
         * @param string $value
         *
		 * @return \Kalibri\View\Page
		 */
		public function &setMetaKeywords( $value )
		{
			$this->setMeta('Keywords', $value);
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get content of single or multiply public files (any located in /Public/ folder).
		 *
         * Easy method for getting content of multiple files like styles, scripts to embed them into page current page.
         * Can be useful when you want decrease page load speed.
         *
         * @throws \Kalibri\Exception
         *
		 * @param string $path Path to public resource in folder that may be accessed thru the web
		 * @param string $publicFolder Name of the public folder.
		 *
		 * @return string
		 */
		public function embedResource( $path, $publicFolder )
		{
			$text = '';

			// Is passed array with multiply files
			if( \is_array( $path ) )
			{
				foreach( $path as $file )
				{
                    $text .= $this->embedResource( $file, $publicFolder );
				}
			}
			else
			{
				// Single file
				if( \file_exists( K_APP_FOLDER."Public/$publicFolder/$path" ) )
				{
					$text = \file_get_contents(K_APP_FOLDER."Public/$publicFolder/$path")."\n";
				}
				else
				{
					throw new \Kalibri\Exception('Resource not found for embedding '.$path.' in '.$publicFolder);
				}
			}

			return $text;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return \Kalibri\View\Page
		 */
		public function &addResource( $text, $container, $key = null )
		{
			$key = $key ?: md5( (string) $text );

			if( \is_array( \Kalibri::data()->get( $container ) ) )
			{
				\Kalibri::data()->set( $container, \array_merge( \Kalibri::data()->get( $container ), [$key=>$text] ));
			}
			else
			{
				\Kalibri::data()->set( $container, \Kalibri::data()->get( $container ).$text);
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return \Kalibri\View\Page
		 */
		public function &embedScript( $path, $asString = false )
		{
			if( $asString )
			{
				return $this->embedResource( $path, 'js');
			}
			else
			{
				$this->addScriptText( $this->embedResource($path, 'js') );
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return \Kalibri\View\Page
		 */
		public function &embedStyle( $path, $asString = false )
		{
			if( $asString )
			{
				return $this->embedResource( $path, 'css');
			}
			else
			{
				$this->addStyleText( $this->embedResource( $path, 'css') );
			}

			return $this;
		}


//------------------------------------------------------------------------------------------------//
		/**
		 * Add stylesheet to the page
		 *
		 * @param string $link Stylesheet link
		 *
		 * @return \Kalibri\View\Page
		 */
		public function addStyle( $link, $media = 'all' )
		{
			if( \is_array( $link ) )
			{
				foreach( $link as $value )
				{
					$this->addStyle( $value, $media );
				}
			}
			else
			{
				if( strlen( $link ) && $link[0] == '/' )
				{
					$link = \Url::css( $link );
				}

				$this->addResource(
						'<link rel="stylesheet" type="text/css" href="'.$link.'" media="'.$media.'"/>',
						\Kalibri\View::VAR_STYLES
				);
			}
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return \Kalibri\View\Page
		 */
		public function &addStyleText( $text )
		{
			$this->addResource( '<style type="text/css">'.$text.'</style>', \Kalibri\View::VAR_STYLES );

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Add link to script for page
		 *
		 * @param string $link
		 *
		 * @return \Kalibri\View\Page
		 */
		public function &addScript( $link )
		{
			if( \is_array( $link ) )
			{
				foreach( $link as $value )
				{
					$this->addScript( $value );
				}
			}
			else
			{
				if( strlen( $link ) && $link[0] == '/' )
				{
					$link = \Url::script( $link );
				}

				$this->addResource(
						'<script type="text/javascript" src="'.$link.'"></script>',
						\Kalibri\View::VAR_SCRIPTS
				);
			}
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Add script text to page
		 *
		 * @param string $text
		 *
		 * @return \Kalibri\View\Page
		 */
		public function &addScriptText( $text )
		{
			if(!str_contains($text, '<script'))
			{
				$text = '<script type="text/javascript">'.$text.'</script>';
			}

			$this->addResource( $text, \Kalibri\View::VAR_SCRIPTS );

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Remove all assigned scripts
		 *
		 * @return \Kalibri\View\Page
		 */
		public function clearScripts()
		{
			\Kalibri::data()->set( \Kalibri\View::VAR_SCRIPTS, [] );

			return $this;
		}
//------------------------------------------------------------------------------------------------//
		/**
		 * Remove all assigned styles
		 *
		 * @return \Kalibri\View\Page
		 */
		public function clearStyles()
		{
			\Kalibri::data()->set( \Kalibri\View::VAR_STYLES, [] );

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Render page with layout and view
		 */
		public function render( $asString = false )
		{
			ob_start();
			header('Content-Type: '.$this->_contentType );

			// Is view or layout passed
			if( !$this->getViewName() && !$this->_layout )
			{
				// Return available page content
				echo \Kalibri::data()->get( \Kalibri\View::VAR_CONTENT );

				return k_ob_get_end( !$asString );
			}

			$data = \Kalibri::data();

			// Convert inner resources and data format from array to string
			$data->set( \Kalibri\View::VAR_SCRIPTS, implode("\n", $data->get( \Kalibri\View::VAR_SCRIPTS ) ) );
			$data->set( \Kalibri\View::VAR_STYLES, implode("\n", $data->get( \Kalibri\View::VAR_STYLES ) ) );
			$data->set( \Kalibri\View::VAR_META, implode("\n", $data->get( \Kalibri\View::VAR_META ) ) );

			// Add prefix and suffix to page title
			$data->set( \Kalibri\View::VAR_TITLE, $this->getTitle(true, true) );

			// Is view name passed and this view exists
			if( $this->_view instanceof \Kalibri\View 
					&& $this->_view->isExists( null, $this->_alternativeViewLocation ) )
			{
				// Render view content into content var ( Kalibri::data()->get( self::VAR_CONTENT ) )
				$this->_view->render( true, $this->_alternativeViewLocation );
			}

			// Is layout name passed and this layout exists
			if( $this->_layout instanceof \Kalibri\View\Layout 
				&& $this->_layout->isExists( null, $this->_alternativeViewLocation ) )
			{
				// Render layout content into content var ( Data::get( self::VAR_CONTENT ) )
				$this->_layout->render( true, $this->_alternativeViewLocation );
			}

			// Send full page content to client
			echo \Kalibri::data()->get( \Kalibri\View::VAR_CONTENT );

			return k_ob_get_end( !$asString );
		}

//------------------------------------------------------------------------------------------------//
		/**
         * Find view for current controller
         *
         * @param string $directory
         * @param string $controller
         * @param string $action
         *
         * @throws \Kalibri\Exception\View\NotFound
         *
         * @return string
		 */
        public function findView( $directory = null, $controller = null, $action = null )
		{
            $directory = $directory ?: \Kalibri::router()->getDirectory();
            $controller = $controller ?: \Kalibri::router()->getController();
            $action = $action ?: \Kalibri::router()->getAction();

			//Is directory set to find in
			if( \strcmp( (string) $directory, '' ) !=0  )
			{
				if( $this->getView()->isExists("$directory/$controller/$action", $this->_alternativeViewLocation) )
				{
					return "$directory/$controller/$action";
				}
				//Directory and controller name
				elseif( $this->getView()->isExists("$directory/$controller", $this->_alternativeViewLocation) )
				{
					return "$directory/$controller";
				}
				//Directory and controller name action
				elseif( $this->getView()->isExists("$directory/{$controller}_$action", $this->_alternativeViewLocation) )
				{
					return "$directory/{$controller}_$action";
				}
			}

			//Controller and method
			if( $this->getView()->isExists("$controller/$action", $this->_alternativeViewLocation) )
			{
				return "$controller/$action";
			}

			//Controller and method
			if( $this->getView()->isExists("{$controller}_{$action}", $this->_alternativeViewLocation) )
			{
				return "{$controller}_{$action}";
			}

			//Controller
			if( $this->getView()->isExists( $controller, $this->_alternativeViewLocation ) )
			{
				return $controller;
			}

			throw new \Kalibri\Exception\View\NotFound("Can't automaticaly find view file ".
				"for controller: '{$controller}' method: '{$action}' directory: '{$directory}'");
		}
	}
}