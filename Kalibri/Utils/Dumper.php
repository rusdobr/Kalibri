<?php

namespace Kalibri\Utils {
	class Dumper
	{
		public static function toSource( $var )
		{
			if( is_string( $var ) )
			{
				return strpos( $var, "'")? '"'.$var.'"': "'$var'";
			}

			if( is_numeric( $var ) )
			{
				return $var;
			}

			if( is_bool( $var ) )
			{
				return $var? 'true':'false';
			}

			if( $var === null )
			{
				return 'null';
			}

			if( is_array( $var ) )
			{
				$return = 'array( ';

				foreach( $var as $key=>$value )
				{
					$key = is_string( $key ) ? (strpos( $key, "'")? '"'.$key.'"': "'$key'"):$key;

					$return .= $key.'=>'.self::toSource( $value ).',';
				}

				return $return.')';
			}
		}
	}
}