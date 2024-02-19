<?php
namespace Kalibri\Helper {

	/**
	 * @package Kalibri
	 * @subpackage Helpers
	 */
	class Date implements \Kalibri\Helper\BaseInterface
	{
		public const SEC_IN_YEAR  = 31536000;
		public const SEC_IN_MONTH = 2592000;
		public const SEC_IN_WEEK  = 604800;
		public const SEC_IN_DAY   = 86400;
		public const SEC_IN_HOUR  = 3600;
		public const SEC_IN_MINUTE= 60;
		public const SEC_IN_HALF_HOUR = 1800;

		#[\Override]
  public static function init( array $options = null ){}

//------------------------------------------------------------------------------------------------//
		public static function dateOrTime( $timestamp, $dateFormat = 'd.m.Y', $timeFormat = 'H:i' )
		{
			return self::isToday( $timestamp )
					? date( $timeFormat, $timestamp )
					: date( $dateFormat, $timestamp );
		}

//------------------------------------------------------------------------------------------------//
		public static function isToday( $timestamp )
		{
            return date('Y-m-d', $timestamp) == date('Y-m-d');
		}

//------------------------------------------------------------------------------------------------//
		public static function secondsToDate( $seconds )
		{
            $diff = (new \DateTime())->diff((new \DateTime())->modify("+{$seconds} seconds"));
            $result = [];

            if($diff->m) {
                $result[] = Text::pluralWith($diff->m, [tr('month'), tr('months'), tr('months')]);
            }

            if($diff->d) {
                $result[] = Text::pluralWith($diff->d, [tr('day'), tr('days'), tr('days')]);
            }

            return implode(' ', $result);
        }

//------------------------------------------------------------------------------------------------//
        public static function secondsToTime($seconds, $short = false)
        {
            $time = [self::SEC_IN_YEAR  => [tr('year'), tr('years'), tr('years')], self::SEC_IN_MONTH => [tr('month'), tr('months'), tr('months')], self::SEC_IN_WEEK  => [tr('week'), tr('weeks'), tr('weeks')], self::SEC_IN_DAY   => [tr('day'), tr('days'), tr('days')], self::SEC_IN_HOUR  => [tr('hour'), tr('hours'), tr('hours')], self::SEC_IN_MINUTE=> [tr('minute'), tr('minutes'), tr('minutes')]];

            $result = '';

            foreach( $time as $sec=>$label )
            {
                if( $seconds >= $sec )
                {
                    $tmp = floor( $seconds / $sec );
                    $seconds -= ceil( $tmp * $sec );

                    $prefix = $short? Text::plural($tmp, $label)[0]: Text::plural($tmp, $label);
                    $result .= $tmp .  " $prefix ";
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
			return floor( ( K_TIME - strtotime( (string) $date ) ) / self::SEC_IN_YEAR );
		}

//------------------------------------------------------------------------------------------------//
		public static function ageFromSeconds( $seconds )
		{
			return floor( $seconds / self::SEC_IN_YEAR );
		}
	}
}