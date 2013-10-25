<?php
namespace Kalibri\Helper {

	/**
	 * @package Kalibri
	 * @subpackage Helpers
	 */
	class Date implements \Kalibri\Helper\BaseInterface
	{
		const SEC_IN_YEAR  = 31536000;
		const SEC_IN_MONTH = 2592000;
		const SEC_IN_WEEK  = 604800;
		const SEC_IN_DAY   = 86400;
		const SEC_IN_HOUR  = 3600;
		const SEC_IN_MINUTE= 60;
		const SEC_IN_HALF_HOUR = 1800;

		public static function init( array $options = null ){}

//------------------------------------------------------------------------------------------------//
		public static function dateOrTime( $timestamp, $dateFormat = 'd.m.Y', $timeFormat = 'H:i' )
		{
			return self::isToday( $timestamp )
					? date( $timeFormat, $timestamp )
					: date( $dateFormat, $timestamp );
		}

//------------------------------------------------------------------------------------------------//
		public static function isToday( $timstamp )
		{
			static $today;

			if( !$today )
			{
				$today = strtotime( date('Y-m-d') );
			}

			return $timstamp > $today && $timstamp < $today + self::SEC_IN_DAY;
		}

//------------------------------------------------------------------------------------------------//
		public static function secondsToTime( $seconds )
		{
			$time = array(
				self::SEC_IN_YEAR=>'y', 
				self::SEC_IN_MONTH=>'m', 
				self::SEC_IN_WEEK=>'w', 
				self::SEC_IN_DAY=>'d', 
				self::SEC_IN_HOUR=>'h', 
				self::SEC_IN_MINUTE=>'m'
			);

			$result = '';

			foreach( $time as $sec=>$label )
			{
				if( $seconds >= $sec )
				{
					$tmp = floor( $seconds / $sec );
					$seconds -= ceil( $tmp * $sec );
					$result .= $tmp.$label.' ';
				}
			}

			if( $seconds > 0 )
			{
				$result .= $seconds.'s';
			}

			return $result;
		}

//------------------------------------------------------------------------------------------------//
		public static function ageFromStr( $date )
		{
			return floor( ( K_TIME - strtotime( $date ) ) / self::SEC_IN_YEAR );
		}

//------------------------------------------------------------------------------------------------//
		public static function ageFromSeconds( $seconds )
		{
			return floor( $seconds / self::SEC_IN_YEAR );
		}
	}
}