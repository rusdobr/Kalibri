<?php
/**
 * Event dispatcher class file
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
	class Dispatcher
	{
		protected $_events;

//------------------------------------------------------------------------------------------------//
		public function &registerHandler( Handler $handler )
		{
			$handledEvents = $handler->getHandledEventsList();

			foreach( $handledEvents as $eventName )
			{
				if( !isset( $this->_events[ $eventName ] ) )
				{
					$this->_events[ $eventName ] = array( 'call'=>array(), 'anon'=>array() );
				}

				$this->_events[ $eventName ]['call'][] = array( &$handler, 'on'.\ucfirst( $eventName ) );
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		public function &registerHandlerFunction( $name, $function )
		{
			if( !isset( $this->_events[ $name ] ) )
			{
				$this->_events[ $name ] = array( 'call'=>array(), 'anon'=>array() );
			}

			if( is_string( $function ) )
			{
				$this->_events[ $name ]['call'][] = array( $name );
			}
			else
			{
				// Lambda function
				$this->_event[ $name ]['anon'][] = &$function;
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		public function &trigger( \Kalibri\Event $event )
		{
			if( isset( $this->_events[ $event->getName() ] ) )
			{
				// Call handlers
				foreach( $this->_events[ $event->getName() ]['call'] as $function )
				{
					\call_user_func_array( $function, array( $event ) );
				}

				$function = null;

				// Lambda handlers
				for( $i = 0; $i < count( $this->_events[ $event->getName() ]['anon'] ); $i++ )
				{
					$function = $this->_events[ $event->getName() ]['anon'][ $i ];
					$function( $event );
				}
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		public function &triggerByName( $eventName )
		{
			$event = new \Kalibri\Event( $eventName, $this );
			$this->trigger( $event );

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		public function getEventsList()
		{
			return array_keys( $this->_events );
		}

//------------------------------------------------------------------------------------------------//
		public function hasHandler( $event )
		{
			$name = $event instanceof \Kalibri\Event? $event->getName(): $event;

			return isset( $this->_events[ $name ] );
		}
	}
}