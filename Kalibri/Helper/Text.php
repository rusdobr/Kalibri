<?php

namespace Kalibri\Helper {

	/**
	 * Text helper
	 * 
	 * @package Kalibri
	 * @subpackage Helpers
	 */
	class Text implements \Kalibri\Helper\BaseInterface
	{
		public static function init( array $options = null ){}

//------------------------------------------------------------------------------------------------//
		/**
		 * Limit amount of words in a string.
		 * 
		 * @param string $str
		 * @param int $limit
		 * @param string $end_char
		 * 
		 * @return string
		 */
		public static function limitWords( $str, $limit = 100, $end_char = '&#8230;' )
		{
			if (trim($str) == '')
			{
				return $str;
			}

			preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

			if (strlen($str) == strlen($matches[0]))
			{
				$end_char = '';
			}

			return rtrim($matches[0]).$end_char;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Limit amount of characters in a string.
		 *
		 * @param string $str
		 * @param int $n
		 * @param string $end_char
		 *
		 * @return string
		 */
		public static function limitCharacters( $str, $n = 500, $end_char = '&#8230;' )
		{
			if (strlen($str) < $n)
			{
				return $str;
			}

			$str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

			if (strlen($str) <= $n)
			{
				return $str;
			}

			$out = "";
			foreach (explode(' ', trim($str)) as $val)
			{
				$out .= $val.' ';

				if (strlen($out) >= $n)
				{
					$out = trim($out);
					return (strlen($out) == strlen($str)) ? $out : $out.$end_char;
				}
			}
		}

//------------------------------------------------------------------------------------------------//
		public static function censor($str, $censored, $replacement = '')
		{
			if ( ! is_array($censored))
			{
				return $str;
			}

			$str = ' '.$str.' ';

			// \w, \b and a few others do not match on a unicode character
			// set for performance reasons. As a result words like über
			// will not match on a word boundary. Instead, we'll assume that
			// a bad word will be bookended by any of these characters.
			$delim = '[-_\'\"`(){}<>\[\]|!?@#%&,.:;^~*+=\/ 0-9\n\r\t]';

			foreach ($censored as $badword)
			{
				if ($replacement != '')
				{
					$str = preg_replace("/({$delim})(".str_replace('\*', '\w*?', preg_quote($badword, '/')).")({$delim})/i", "\\1{$replacement}\\3", $str);
				}
				else
				{
					$str = preg_replace("/({$delim})(".str_replace('\*', '\w*?', preg_quote($badword, '/')).")({$delim})/ie", "'\\1'.str_repeat('#', strlen('\\2')).'\\3'", $str);
				}
			}

			return trim( $str );
		}
	}
}