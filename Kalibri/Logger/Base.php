<?php

namespace Kalibri\Logger {

	/**
	 * @package Kalibri
	 */
	abstract class Base
	{
		const L_WARNING = 'Warning';
		const L_DEBUG   = 'Debug';
		const L_ERROR   = 'Error';
		const L_INFO    = 'Info';
		const L_EXCEPTION = 'Exception';

		const DEFAULT_DATE_FOTRMAT = 'Y-m-d H:i:s';

		protected $_messages = array();
		protected $_excludedLevels = array();		
		protected $_options = array();
		protected $_uniq;
		
//------------------------------------------------------------------------------------------------//
		/**
		 * Add new log message
		 *
		 * @param string $level
		 * @param string $message
		 * @param Object $class Class from what added message
		 * 
		 * @assert ('Error', 'msg') == true
		 * @assert ('excluded_level', 'msg') == false
		 */
		abstract public function add( $level, $message, $class = NULL );

//------------------------------------------------------------------------------------------------//
		/**
		 * Clear logged messages
		 */
		abstract public function clear();

//------------------------------------------------------------------------------------------------//
		/**
		 * Write logged messages to file
		 * 
		 * @assert () == false
		 * @assert () == true
		 */
		abstract public function write();

//------------------------------------------------------------------------------------------------//
		public function __construct( array $options = null )
		{
			$this->init( $options );
		}

//------------------------------------------------------------------------------------------------//
		public function init( array $options = null )
		{
			if( $options )
			{
				$this->_options = $options;
			}

			// Load options from config if not initialized yet
			if( !$this->_options )
			{
				$this->_options = \Kalibri::config()->get('debug.log');
			}

			// Set default date format if skipped in config
			if( !isset( $this->_options['date-format'] ) )
			{
				$this->_options['date-format'] = self::DEFAULT_DATE_FOTRMAT;
			}
			
			$this->_uniq = uniqid();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get all logged message til now
		 *
		 * @assert () == array
		 * 
		 * @return array
		 */
		public function getMessages()
		{
			return $this->_messages;
		}

//------------------------------------------------------------------------------------------------//
		public function getAsString( $format = null )
		{
			if( !$format )
			{
				$format = $this->_options['format'];
			}

			$str = "";
			foreach( $this->_messages as $msg )
			{
				$str .= str_replace( 
					array('%date', '%level', '%class', '%msg', '%uniq'), 
					array( $msg['date'], $msg['level'], $msg['class'], $msg['msg'], $this->_uniq ),
					$format 
				);
			}

			return $str;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @assert () == false
		 * @assert () == true
		 * 
		 * @return bool
		 */
		public function isErrorsAvailable()
		{
			foreach( $this->_messages as $msg )
			{
				if( $msg['level'] == self::L_ERROR
					|| $msg['level'] == self::L_EXCEPTION
					|| $msg['level'] == self::L_WARNING
				)
				{
					return true;
				}
			}

			return false;
		}

//------------------------------------------------------------------------------------------------//
		public function excludeLevel( $name )
		{
			$this->_excludedLevels[ $name ] = true;
		}

//------------------------------------------------------------------------------------------------//
		public function excludeLevels( array $levels )
		{
			$this->_excludedLevels = \array_merge( $this->_excludedLevels, $levels );
		}
	}
}