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
		public static function isToday( $timestamp )
		{
			static $today;

			if( !$today )
			{
				$today = strtotime( date('Y-m-d') );
			}

			return $timestamp > $today && $timestamp < $today + self::SEC_IN_DAY;
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

        public function secondsToTime($seconds)
        {
            $time = array(
                self::SEC_IN_YEAR  => [tr('year'), tr('years'), tr('years')],
                self::SEC_IN_MONTH => [tr('month'), tr('months'), tr('months')],
                self::SEC_IN_WEEK  => [tr('week'), tr('weeks'), tr('weeks')],
                self::SEC_IN_DAY   => [tr('day'), tr('days'), tr('days')],
                self::SEC_IN_HOUR  => [tr('hour'), tr('hours'), tr('hours')],
                self::SEC_IN_MINUTE=> [tr('minute'), tr('minutes'), tr('minutes')]
            );

            $result = '';

            foreach( $time as $sec=>$label )
            {
                if( $seconds >= $sec )
                {
                    $tmp = floor( $seconds / $sec );
                    $seconds -= ceil( $tmp * $sec );
                    if(is_array($label)) {
                        $result .= $tmp . ' '.Text::plural($tmp, $label) . ' ';
                    } else {
                        $result .= $tmp . $label . ' ';
                    }
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