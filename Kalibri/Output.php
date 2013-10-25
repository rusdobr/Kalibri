<?php
/**
 * Kalibri Output class file
 * 
 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
 */

namespace Kalibri {

	/**
	 * Kalibri Output responsible for output catching.
	 * 
	 * @version 0.1
	 * @package Kalibri
	 * @since 0.1
	 */
	class Output
	{
//------------------------------------------------------------------------------------------------//
		/**
		 * Start caching output
		 *
		 * @return null
		 */
		public function start()
		{
			ob_start();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Complete caching output and print it
		 *
		 * @param bool $print Is output must be sendet
		 *
		 * @return string
		 */
		public function &complete( $print = true )
		{
			$output = ob_get_clean();

			if( $print )
			{
				header('Content-Length:'.strlen( $output ));
				echo $output;
				return $output;
			}

			return $output;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Clear all output catched by last start()
		 *
		 * @return NULL
		 */
		public function clear()
		{
			ob_clean();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Stop caching and return output as string
		 *
		 * @return string
		 */
		public function &stop()
		{
			$buffer = ob_get_clean();
			ob_end_clean();
			return $buffer;
		}
	}
}