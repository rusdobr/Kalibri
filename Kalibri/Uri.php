<?php
/**
 * Kalibri Uri class file
 * 
 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
 */

namespace Kalibri;

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
    protected $uri = '';

    /**
     * Splitted URL
     * @var array,string
     */
    protected $segments = array();

//------------------------------------------------------------------------------------------------//
    /**
     * \Kalibri\Uri constructor
     *
     * @param string $requestUri Set uri to prevent fetching
     */
    public function __construct( $requestUri = null )
    {
        if( !$requestUri )
        {
            $this->fetchUri();
        }
        else
        {

            $this->uri = $requestUri;
        }

        $this->process();
    }

//------------------------------------------------------------------------------------------------//
    /**
     * @return string
     */
    protected function fetchUri()
    {
        /**
         * If the URL has a question mark then it's simplest to just
         * build the URI string from the zero index of the $_GET array.
         * This avoids having to deal with $_SERVER variables, which
         * can be unreliable in some environments
         */
        if( count( $_GET ) && strpos( key( $_GET ), '/') === 0 )
        {
            return $this->uri = key( $_GET );
        }

        /**
         * Is there a PATH_INFO variable?
         * Note: some servers seem to have trouble with getenv() so we'll test it two ways
         */
        $path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');

        if( trim( $path, '/' ) != '' && $path != '/'.\Kalibri::config()->get('entry') )
        {
            return $this->uri = $path;
        }

        // No PATH_INFO?... What about QUERY_STRING?
        $path =  (isset($_SERVER['QUERY_STRING']))
            ? $_SERVER['QUERY_STRING']
            : @getenv('QUERY_STRING');

        if( trim( $path, '/' ) != '')
        {
            return $this->uri = $path;
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
            return $this->uri = $path;
        }

        // We've exhausted all our options...
        return $this->uri = '';
    }

//------------------------------------------------------------------------------------------------//
    /**
     * @assert () == ''
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

//------------------------------------------------------------------------------------------------//
    /**
     * @access protected
     */
    protected function process()
    {
        $indexPage = \Kalibri::config()->get('entry');
        $indexPage = $indexPage[0] && $indexPage[0] != '/'? '/'.$indexPage: $indexPage;
        $uri = str_replace( $indexPage, '', $this->uri );

        //Check is URI valid
        if( !$this->isValidUri( $uri ) )
        {
            \Kalibri::error()->show('Invalid URI chars');
        }

        //Set default uri and clear segments
        $this->uri = '/';
        $this->segments = array();

        if( strlen($uri) === 0 )
        {
            $uri = '/';
        }
        elseif( isset( $uri[0] ) && $uri[0] == '?' )
        {
            $uri = substr( $uri, 1 );
        }

        $uri = $uri == ''? '/': $uri;
        //Remove first slash
        $uri = $uri[0] == '/' && strlen( $uri ) > 1? substr( $uri, 1, strlen( $uri ) ): $uri;

        if( strpos($uri, '/') !== false )
        {
            $this->segments = explode('/', $uri);
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
     * @param int $index
     * @return string,NULL
     */
    public function getSegment( $index )
    {
        return isset( $this->segments[ $index ] )? $this->segments[ $index ]: null;
    }

//------------------------------------------------------------------------------------------------//
    /**
     * @param int $offset
     *
     * @return array
     */
    public function getSegments( $offset = 0 )
    {
        if( $offset == 0)
        {
            return $this->segments;
        }
        elseif( isset( $this->segments[ $offset ] ) )
        {
            return array_slice( $this->segments, $offset );
        }

        return array();
    }

//------------------------------------------------------------------------------------------------//
    /**
     * @param array $segments
     *
     * @return \Kalibri\Uri
     */
    public function &setSegments( array $segments )
    {
        $this->segments = &$segments;
        return $this;
    }

//------------------------------------------------------------------------------------------------//
    /**
     * Set new uri
     *
     * @param string $uri
     *
     * @throws Exception\Invalid\Param
     * @return boolean
     */
    public function setUri( $uri )
    {
        if( $this->isValidUri( $uri ) )
        {
            $this->uri = $uri;
            $this->process();

            return $this;
        }

        throw new \Kalibri\Exception\Invalid\Param('Passed invalid uri to Uri::setUri');
    }
}