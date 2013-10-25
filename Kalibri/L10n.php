<?php

namespace Kalibri {

	class L10n
	{
		protected $_currentLang;
		protected $_languages;
		protected $_messages = array();
		protected $_isTranslationAllowed = false;

//------------------------------------------------------------------------------------------------//
		public function __construct()
		{
			$config = \Kalibri::config()->get('l10n');

			$this->_isTranslationAllowed = isset( $config['is-allowed'] ) 
				&& $config['is-allowed'];

			if( $this->_isTranslationAllowed )
			{
				$this->_currentLang = $config['language'];
				$this->_languages = isset( $config['languages'] )
					? $config['languages']
					: array( $this->_currentLang=>$this->_currentLang );

				$this->load( $this->_currentLang );
			}
		}

//------------------------------------------------------------------------------------------------//
		public function getLanguages()
		{
			return $this->_languages;
		}

//------------------------------------------------------------------------------------------------//
		public function setCurrentByName( $shortName )
		{
			if( isset( $this->_languages[ $shortName ] ) )
			{
				$this->_currentLang = $shortName;
				return $this;
			}

			throw new \Kalibri\Exception("Language '$shortName' not allowed");
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get current language
		 * 
		 * @param string $shortName
		 * 
		 * @return string
		 */
		public function getCurrent( $short = true )
		{
			return $short ? $this->_currentLang : $this->_languages[ $this->_currentLang ];
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get language short name by it's long name
		 * 
		 * @parm string $language
		 * 
		 * @return string|null
		 */
		public function getShortName( $fullName = null )
		{
			foreach( $this->_languages as $short=>$full )
			{
				if( $full === $fullName )
				{
					return $short;
				}
			}

			return null;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get language full name
		 * 
		 * @param string $shortName
		 * 
		 * @return string|null
		 */
		public function getFullName( $shortName = null )
		{
			$shortName = $shortName?: $this->_currentLang;
			return isset( $this->_languages[ $shortName ] ) ? $this->_languages[ $shortName ]: null;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Translate string
		 * 
		 * @param string $key
		 * @param array $params
		 * 
		 * @return string
		 */
		public function tr( $key, array $params = null)
		{
			$result = $key;

			if( $this->_isTranslationAllowed && isset( $this->_messages[ $this->_currentLang ][ $key ] ) )
			{
				$result = $this->_messages[ $this->_currentLang ][ $key ];
			}

			// Insert passed params (if any) into string
			if( is_array( $params ) && count( $params ) )
			{
				foreach( $params as $var=>$value )
				{
					$result = str_replace( ':'.$var, $value, $result );
				}
			}

			return $result;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get all messages for specified language
		 * 
		 * @param string $shortName Language short name like 'en', 'ru' ...
		 * 
		 * @return array|null
		 */
		public function getMessages( $shortName = null )
		{
			$shortName = $shortName?:$this->_currentLang;
			return isset( $this->_messages[ $shortName ] )? $this->_messages[ $shortName ]: array();
		}

//------------------------------------------------------------------------------------------------//
		public function load( $shortName = null )
		{
			$shortName = $shortName?: $this->_currentLang;
			$appLocation = \Kalibri::app()->getLocation();

			$locations = array(
				$appLocation.'/../Kalibri/Data/Locale/'.$shortName.'/',
				$appLocation.'/Locale/'.$shortName.'/'
			);

			if( !isset( $this->_messages[ $shortName ] ) )
			{
				$this->_messages[ $shortName ] = array();
			}

			foreach( $locations as $location )
			{
				if( is_dir( $location ) )
				{
					foreach( scandir( $location ) as $fileName )
					{
						if( !is_dir( $location.$fileName ) )
						{
							$this->_messages[ $shortName ] = array_merge( 
								$this->_messages[ $shortName ], 
								include( $location.$fileName ) 
							);
						}
					}
				}
			}
		}
	}
}