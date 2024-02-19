<?php

namespace Kalibri\Helper {

	/**
	 * @package Kalibri
	 * @subpackage Helpers
	 */
	class Url implements \Kalibri\Helper\BaseInterface
	{
		protected static $_imagesDir;
		protected static $_stylesDir;
		protected static $_jsDir;

		protected static $_isInitialized = false;

        protected static $entry;
		protected static $base;
		protected static $baseUrl;
		protected static $baseDomainedUrl;
		
//------------------------------------------------------------------------------------------------//
		#[\Override]
  public static function init( array $options = null ): void
		{
			if( self::$_isInitialized )
            {
                return;
            }

            if( !$options )
            {
                $options = \Kalibri::config()->get('resources');
            }

            self::$_imagesDir = $options['images'];
            self::$_jsDir = $options['js'];
            self::$_stylesDir = $options['styles'];

            $config = \Kalibri::config()->getAll();

            self::$base = $config['base'][ strlen( (string) $config['base'] ) -1 ] == '/'
                ? substr( (string) $config['base'], 0, -1)
                : $config['base'];

            self::$entry = $config['entry'];
            self::$baseUrl = self::$base.self::$entry;
            self::$baseDomainedUrl = 'http://%d%'.$config['base-host'].'/'.$config['entry'];

            if( self::$entry[ strlen( (string) self::$entry ) -1 ] == '/' )
            {
                self::$baseDomainedUrl = substr( self::$baseDomainedUrl, 0, -1 );
                self::$baseUrl = substr( self::$baseUrl, 0, -1 );
            }

            self::$_isInitialized = true;
		}

//------------------------------------------------------------------------------------------------//
		public static function setImagesDir( $path ): void
		{
			if( !empty( $path ) && $path[ strlen( (string) $path )-1 ] !== '/' )
			{
				$path .= '/';
			}

			self::$_imagesDir = $path;
		}

//------------------------------------------------------------------------------------------------//
		public static function getImagesDir()
		{
			return self::$_imagesDir;
		}

//------------------------------------------------------------------------------------------------//
		public static function setStylesDir( $path ): void
		{
			if( !empty( $path ) && $path[ strlen( (string) $path )-1 ] !== '/' )
			{
				$path .= '/';
			}

			self::$_stylesDir = $path;
		}
		
//------------------------------------------------------------------------------------------------//
		public static function setScriptsDir( $path ): void
		{
			if( !empty( $path ) && $path[ strlen( (string) $path )-1 ] !== '/' )
			{
				$path .= '/';
			}

			self::$_jsDir = $path;
		}

//------------------------------------------------------------------------------------------------//
		public static function site( $path = '', $subdomain = null )
		{
            $root = self::$baseUrl;

            if( $subdomain !== null )
            {
                $root = str_replace('%d%', $subdomain, (string) self::$baseDomainedUrl);
            }

			$path = $path && $path[0] == '/'? substr((string) $path, 1): $path;
			
			return $root.($root[strlen((string) $root)-1] != '/'? '/': '').$path;
		}

//------------------------------------------------------------------------------------------------//
		public static function resource( $path = '' )
		{
			return self::$base.( $path && $path[0] == '/'? '':'/' ). str_replace( '//', '/', (string) $path );
		}

//------------------------------------------------------------------------------------------------//
		public static function image( $path )
		{
			return self::resource( self::$_imagesDir.$path );
		}

//------------------------------------------------------------------------------------------------//
		public static function css( $path )
		{
			return self::resource( self::$_stylesDir.$path );
		}

//------------------------------------------------------------------------------------------------//
		public static function script( $path )
		{
			return self::resource( self::$_jsDir.$path );
		}

//------------------------------------------------------------------------------------------------//
		public static function title( $str, $separator = 'dash', $lowercase = FALSE )
		{
			$search		= '-';
			$replace	= '_';

			if ($separator == 'dash')
			{
				$search		= '_';
				$replace	= '-';
			}

			$trans = ['&\#\d+?;'				=> '', '&\S+?;'				=> '', '\s+'					=> $replace, '[^a-z0-9\-\._]'		=> '', $search.'+'			=> $replace, $search.'$'			=> $replace, '^'.$search			=> $replace, '\.+$'					=> ''];

			$str = strip_tags( (string) $str );

			foreach( $trans as $key=>$val )
			{
				$str = preg_replace("#".$key."#i", $val, (string) $str);
			}

			if ($lowercase === TRUE)
			{
				$str = strtolower((string) $str);
			}

			return trim( stripslashes( (string) $str ) );
		}

//------------------------------------------------------------------------------------------------//
		public static function redirect( $uri = '', $method = 'location', $httpResponseCode = 302 ): void
		{
			if( ! preg_match('#^https?://#i', (string) $uri) )
			{
				$uri = self::site( $uri );
			}

			match ($method) {
       'refresh' => header("Refresh:0;url=$uri"),
       default => header("Location: $uri", true, $httpResponseCode),
   };
			exit;
		}

//------------------------------------------------------------------------------------------------//
		public static function current( $fullPath = false )
		{
			/**
			 * @todo Add protocol detection
			 */
			$uri = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$root = \Kalibri::config()->get('base').\Kalibri::config()->get('entry');

			return $fullPath ? $uri: str_replace( $root, '', $uri );
		}
	}

	\Kalibri\Helper\Url::init();
}