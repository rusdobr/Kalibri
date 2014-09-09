<?php
/**
 * Kalibri Uri class file
 * 
 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
 */

namespace Kalibri {

	/**
	 * Kalibri\Uri class responsible for getting routing segments for Router class.
	 * It tries to get them from $_GET, $_SERVER['PATH_INFO'], $_SERVER['QUERY_STRING'] and
	 * $_SERVER['ORIG_PATH_INFO']. All this sources should guarantee good routing segments.
	 * 
	 * @version 0.2
	 * @package Kalibri
	 * @since 0.1
     *
     * @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
	 */
	class Uri
	{
		/**
		 * @var string
		 */
		protected $_uri = '';

		/**
		 * Splitted URL
		 * @var array,string
		 */
		protected $_segments = array();

//------------------------------------------------------------------------------------------------//
		/**
		 * Kalibri\Uri constructor
		 *
		 * @param string $requestUri Set uri to prevent fetching
		 */
		public function __construct( $requestUri = NULL )
		{
			if( !$requestUri )
			{
				$this->_fetchUri();
			}
			else
			{

				$this->_uri = $requestUri;
			}
			
			$this->_process();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return string
		 */
		protected function _fetchUri()
		{
			/** 
			 * If the URL has a question mark then it's simplest to just
			 * build the URI string from the zero index of the $_GET array.
			 * This avoids having to deal with $_SERVER variables, which
			 * can be unreliable in some environments
			 */
			if( count( $_GET ) && strpos( key( $_GET ), '/') === 0 )
			{
				return $this->_uri = key( $_GET );
			}

			/**
			 * Is there a PATH_INFO variable?
			 * Note: some servers seem to have trouble with getenv() so we'll test it two ways
			 */
			$path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');

			if( trim( $path, '/' ) != '' && $path != '/'.\Kalibri::config()->get('entry') )
			{
				return $this->_uri = $path;
			}

			// No PATH_INFO?... What about QUERY_STRING?
			$path =  (isset($_SERVER['QUERY_STRING']))
				? $_SERVER['QUERY_STRING']
				: @getenv('QUERY_STRING');

			if( trim( $path, '/' ) != '')
			{
				return $this->_uri = $path;
			}

			// No QUERY_STRING?... Maybe the ORIG_PATH_INFO variable exists?
			$path = str_replace(
					$_SERVER['SCRIPT_NAME'],
					'',
					(isset($_SERVER['ORIG_PATH_INFO']))
						? $_SERVER['ORIG_PATH_INFO']
						: @getenv('ORIG_PATH_INFO')
			);

			if( trim( $path, '/' ) != '' && $path != '/'.\Kalibri::config()->get('entry') )
			{
				// remove path and script information so we have good URI data
				return $this->_uri = $path;
			}

			// We've exhausted all our options...
			return $this->_uri = '';
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @assert () == ''
		 * 
		 * @return string
		 */
		public function getUri()
		{
			return $this->_uri;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @access protected
		 */
		protected function _process()
		{
			$index_page = \Kalibri::config()->get('entry');
			$uri = $this->_uri;

			//Add slash to the index page
			if( isset( $index_page[0] ) && $index_page[0] != '/' )
			{
				$index_page = '/'.$index_page;
			}

			//Remove index page from uri
			if( strlen( $uri ) > 0 )
			{
				$uri = str_replace( $index_page, '', $uri );
			}
			else
			{
				$uri = '/';
			}

			//Remove ? from uri
			if( isset( $uri[0] ) && $uri[0] == '?' )
			{
				$uri = substr( $uri, 1 );
			}

			if( $uri == '' )
			{
				$uri = '/';
			}

			//Check is URI valid
			if( !$this->isValidUri( $uri ) )
			{
				\Kalibri::error()->show('Invalid URI chars');
			}

			//Remove first slash
			if( $uri[0] == '/' && strlen( $uri ) > 1 )
			{
				$uri = substr( $uri, 1, strlen( $uri ) );
			}
			
			//Split uri to segments
			$this->_segments = explode( '/', $uri );
			//Check is URI was empty
			if( $this->_segments[0] == '' )
			{
				//Set default uri and clear segments
				$this->_uri = '/';
				$this->_segments = array();
			}
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @param string
		 * @return bool
		 */
		public function isValidUri( $uri )
		{
			return (bool)preg_match("/^[".\Kalibri::config()->get('permitted-uri-chars')."\/]+$/", $uri );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return string,NULL
		 */
		public function getSegment( $index )
		{
			return isset( $this->_segments[ $index ] )? $this->_segments[ $index ]: null;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return array
		 */
		public function getSegments( $offset = 0 )
		{
			if( $offset == 0)
			{
				return $this->_segments;
			}
			elseif( isset( $this->_segments[ $offset ] ) )
			{
				return array_slice( $this->_segments, $offset );
			}

			return array();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return Kalibri\Uri
		 */
		public function &setSegments( array $segments )
		{
			$this->_segments = &$segments;
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Set new uri
		 * 
		 * @param string $uri
		 * 
		 * @return boolean
		 */
		public function setUri( $uri )
		{
			if( $this->isValidUri( $uri ) )
			{
				$this->_uri = $uri;
				$this->_process();

				return $this;
			}

			throw new \Kalibri\Exception\Invalid\Param('Passed invalid uri to Uri::setUri');
		}
	}
}