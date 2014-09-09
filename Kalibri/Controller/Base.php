<?php
/**
 * Base controller class file
 * 
 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
 */

namespace Kalibri\Controller {

	/**
	 * Base controller class
	 * 
	 * @version 0.3
	 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
	 * @since 0.1
	 */
	class Base
	{
		/**
		 * This flag shows is controller already rendered
		 * @var bool
		 */
		protected $_isRendered = false;

//------------------------------------------------------------------------------------------------//
		public function isRendered( $newValue = null )
		{
			if( $newValue !== null )
			{
				$this->_isRendered = (bool) $newValue;
			}

			return $this->_isRendered;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Render page and send output
		 * 
		 * @param bool $asString Option to get rendered page as string without outputting
		 * 
		 * @return mixed
		 */
		public function _render( $asString = false )
		{
			$this->_isRendered = true;
		}
	}
}