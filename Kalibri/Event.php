<?php

namespace Kalibri;

/**
 * @version 0.1
 * @package Kalibri
 * @subpackage Event
 * @since 0.3
 *
 * @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
 */
class Event
{
	protected $_events;

//------------------------------------------------------------------------------------------------//
	public function &registerHandler( $name, $function )
	{
		if( !isset( $this->_events[ $name ] ) )
		{
			$this->_events[ $name ] = [];
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
