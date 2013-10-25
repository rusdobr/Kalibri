<?php
/**
 * Event Handler base class file
 * 
 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
 */

namespace Kalibri\Event {

	/**
	 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
	 * @version 0.1
	 * @package Kalibri
	 * @subpackage Event
	 * @since 0.3
	 */
	class Handler
	{
//------------------------------------------------------------------------------------------------//
		public function run( $name, \Kalibri\Event $event )
		{
			$fullName = 'on'.\ucfirst( $name );

			if( in_array($fullName, get_class_methods( $this ) ) )
			{
				\call_user_func_array( array( $this, $fullName ), array( $event ) );
			}
		}

//------------------------------------------------------------------------------------------------//
		public function getHandledEventsList()
		{
			$list = array();

			foreach( get_class_methods( $this ) as $method )
			{
				if( strpos( $method, 'on' ) === 0 )
				{
					// Add to list without 'on' prefix
					$list[] = strtolower( substr( $method, 2 ) );
				}
			}

			return $list;
		}
	}
}