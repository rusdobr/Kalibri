<?php

/**
 * @todo: Use new validation classes from Kalibri\Validation
 */
namespace Kalibri\Helper {

	/**
	 * @package Kalibri
	 * @subpackage Helpers
	 */
	class Validate
	{
		public const RULE_FIELDS = 0;
		public const RULE_OPERATION = 1;

		public static $_lastError;
//------------------------------------------------------------------------------------------------//
		/**
		 * Check is var valid email address
		 *
		 * @param string $email
		 *
		 * @return mixed(string,bool)
		 */
		public static function email( $email )
		{
			return filter_var( $email, FILTER_VALIDATE_EMAIL );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Check is var valid IP adress
		 *
		 * @param string $ip IP address to check
		 *
		 * @return mixed(string,bool)
		 */
		public static function ip( $ip, $isIP6 = false )
		{
			if( $isIP6 )
			{
				return filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 );
			}

			return filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Check is var contains valid url. Optionaly can be checked with scheme.
		 *
		 * @param string $url URL to check
		 * @param bool $withScheme Is scheme will be checked
		 * @param bool $withPath Is path required
		 * @param bool $withQuery Is query part required
		 *
		 * @return mixed(string,bool)
		 */
		public static function url( $url, $withSchema = false, $withPath = false, $withQuery = false )
		{
			$flags = 0;

			if( $withSchema )
			{
				$flags |= FILTER_FLAG_SCHEME_REQUIRED;
			}

			if( $withPath )
			{
				$flags |= FILTER_FLAG_PATH_REQUIRED;
			}

			if( $withQuery )
			{
				$flags |= FILTER_FLAG_QUERY_REQUIRED;
			}

			return filter_var( $url, FILTER_VALIDATE_URL, $flags );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Validate text as regular expression
		 *
		 * @param string $regexp Regular expression to check
		 *
		 * @return mixed(string,bool)
		 */
		public static function regexp( $regexp )
		{
			return filter_var( $regexp, FILTER_VALIDATE_REGEXP );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Check is $text is valid int value and optionaly in specified range.
		 *
		 * @param int $int
		 * @param int $min Minimal int value
		 * @param int $max Maximum int value
		 *
		 * @return mixed(int,bool)
		 */
		public static function int( $int, $min = null, $max = null )
		{
			$options = [];

			if( $min !== null )
			{
				$options['min_range'] = $min;
			}

			if( $max !== null )
			{
				$options['max_range'] = $max;
			}

			return filter_var( $int, FILTER_VALIDATE_INT, $options );
		}
		
//------------------------------------------------------------------------------------------------//
		public static function string(  ){}

//------------------------------------------------------------------------------------------------//
		public static function rule( $data, $rule )
		{
			$fields = $rule[ self::RULE_FIELDS ];

			if( !is_array( $rule[ self::RULE_FIELDS ] ) )
			{
				$fields = [$rule[ self::RULE_FIELDS ]];
			}

			self::$_lastError = [];

			switch( $rule[ self::RULE_OPERATION ] )
			{
				case 'string':

					break;
				case 'required':
						foreach( $fields as $field )
						{
							if( !isset( $data[ $field ]['value'] ) || empty( $data[ $field ]['value'] ) )
							{
								self::$_lastError[] = tr("Field :name is required\n", ['name'=>$field]);
							}
						}
					break;
				case 'equal':
						foreach( $fields as $field )
						{
							if( 
								!isset( $data[ $field ]['value'] ) 
								|| empty( $data[ $field ]['value'] ) 
								|| $data[ $field ]['value'] != $data[ $rule['with'] ]['value']
							)
							{
								self::$_lastError[] = tr("Field :name1 and :name2 not equal", ['name1'=>$field, 'name2'=>$rule['with']]);
							}
						}
					break;
			}

			return count( self::$_lastError ) == 0;
		}

//------------------------------------------------------------------------------------------------//
		public static function getLastErrors()
		{
			return self::$_lastError;
		}
	}
}