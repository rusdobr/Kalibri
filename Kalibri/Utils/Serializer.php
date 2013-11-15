<?php

namespace Kalibri\Utils {

	/**
	 * @package Kalibri
	 * @subpackage Utils
	 */
	class Serializer
	{
//------------------------------------------------------------------------------------------------//
		public static function toArray( $obj )
		{
			if( is_array( $obj ) && count( $obj ) )
			{
				foreach( $obj as $key=>$value )
				{
					if( is_object( $value ) || is_array( $value ) )
					{
						$value = self::toArray( $value );
					}

					$obj[ $key ] = $value;
				}
			}
			elseif( is_object( $obj ) )
			{
				$class_name = get_class( $obj );
				if( preg_match( '/^O:\d+:.+?:(\d+:\{.*)$/', serialize( $obj ), $match ) )
				{
					$obj = unserialize( 'a:'.$match[1] );

					foreach( $obj as $key=>$value )
					{
						$key1 = $key;

						// Remove protected property flag \0*\0
						if( $key[1] == "*" )
						{
							$key = substr( $key, 3 );
						}

						// Remove private property flag \0CLASS_NAME\0
						if( strpos( $key, $class_name ) )
						{
							$key = substr( $key, strlen( $class_name ) + 2 );
						}

						// Remove leading '_'
						if( $key[0] == '_' )
						{
							$key = substr($key, 1);
						}

						unset( $obj[ $key1 ] );

						if( is_object( $value ) || is_array( $value ) )
						{
							$value = self::toArray( $value );
						}

						$obj[ $key ] = $value;
					}
				}
			}

			return $obj;
		}

//------------------------------------------------------------------------------------------------//
		public static function toJSON( $obj )
		{
			if( is_object( $obj ) || is_array( $obj ) )
			{
				$obj = self::toArray( $obj );
			}

			return json_encode( $obj );
		}

//------------------------------------------------------------------------------------------------//
		public static function toXML( $obj, $rootNodeName = '', $returnHTMLValues = FALSE, $addHead = TRUE, $noConvert = FALSE )
		{
			$xml = '';
			$subNodeName = 'node';

			if( !$noConvert && ( is_object( $obj ) || is_array( $obj ) ) )
			{
				$obj = self::toArray( $obj );
			}

			if( !empty( $rootNodeName ) )
			{
				$subNodeName = substr( $rootNodeName, 0, strlen( $rootNodeName )-1 );
			}

			if( is_array( $obj ) )
			{
				foreach( $obj as $key=>$value )
				{
					if( is_numeric( $key ) )
					{
						$key = $subNodeName;
					}

					if( is_array( $value ) )
					{
						$xml .= self::toXML( $value, $key, $returnHTMLValues, FALSE, TRUE );
					}
					else
					{
						if( !$returnHTMLValues )
						{
							// Escape html/xml tags
							$value = htmlentities( $value );
						}
						else
						{
							if( is_string( $value ) && ( strpos( $value, '<' ) || strpos( $value, '>' ) )  )
							{
								$value = "<![CDATA[$value]]>";
							}
						}

						$xml .= "<{$key}>{$value}</{$key}>\n";
					}
				}
			}
			else
			{
				throw new Exception("Invalid param type. Array or Object required!");
			}

			if( !empty( $rootNodeName ) )
			{
				$xml = "<{$rootNodeName}>\n{$xml}\n</{$rootNodeName}>";
			}

			if( $addHead )
			{
				$xml = '<?xml version="1.0" encoding="UTF-8" ?>'.$xml;
			}

			return $xml;
		}
	}
}