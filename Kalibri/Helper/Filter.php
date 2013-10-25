<?php

namespace Kalibri\Helper {

	/**
	 * @package Kalibri
	 * @subpackage Helpers
	 */
	class filter implements \Kalibri\Helper\BaseInterface
	{
		public static function init( array $options = null ){}

//------------------------------------------------------------------------------------------------//
		/**
		 * Filter string from invalid email characters
		 * 
		 * @param string $text
		 * 
		 * @return mixed(string,bool)
		 */
		public static function email( $text )
		{
			return filter_var( $text, FILTER_SANITIZE_EMAIL );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Filter string from not number characters
		 *
		 * @param int $int
		 *
		 * @return mixed(string,bool)
		 */
		public static function int( $int )
		{
			return filter_var( $int, FILTER_SANITIZE_NUMBER_INT );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Filter string from potentially dengeres characters.
		 * 
		 * @param string $text
		 * 
		 * @return string
		 */
		public static function string( $text )
		{
			return filter_var( $text, FILTER_SANITIZE_STRIPPED );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Check is $text valid float value
		 */
		public static function float( $text )
		{
			return filter_var( $text, FILTER_SANITIZE_NUMBER_FLOAT );
		}

//------------------------------------------------------------------------------------------------//
		public static function regexp( $regexp )
		{
			/**
			 * @todo FILTER REGEXP
			 */
		}
	}
}