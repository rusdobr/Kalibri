<?php
/**
 * Event dispatcher class file
 * 
 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
 */

namespace Kalibri {

	/**
	 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
	 * @version 0.1
	 * @package Kalibri
	 * @subpackage Event
	 * @since 0.3
	 */
	class Event
	{
		protected $_events;

//------------------------------------------------------------------------------------------------//
		public function &registerHandler( $name, $function )
		{
			if( !isset( $this->_events[ $name ] ) )
			{
				$this->_events[ $name ] = array();
			}

			$this->_events[ $name ][] = &$function;

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		public function &trigger( $eventName )
		{
			
			if( isset( $this->_events[ $eventName ] ) && ( $count = count( $this->_events[ $eventName ] ) ) )
			{
				for( $i = 0; $i < $count; $i++ )
				{
					$function = $this->_events[ $eventName ][ $i ];
					$function();
				}
			}
			
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		public function getEventsList()
		{
			return array_keys( $this->_events );
		}

//------------------------------------------------------------------------------------------------//
		public function hasHandler( $eventName )
		{
			return isset( $this->_events[ $eventName ] );
		}
	}
}