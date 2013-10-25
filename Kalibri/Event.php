<?php
/**
 * Kalibri Event class file
 * 
 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
 */

namespace Kalibri {

	/**
	 * Event class represents simple event implementation. This class is an atom in 
	 * event system of Kalibri framework
	 * 
	 * @author Alexander Kostinenko aka tenebras <kostinenko@gmail.com>
	 * @version 0.1
	 * @package Kalibri
	 * @since 0.3
	 */
	class Event
	{
		/**
		 * Link to class who owns this event
		 * @var object
		 */
		protected $_parent;

		/**
		 * Event name
		 * @var string
		 */
		protected $_name;

//------------------------------------------------------------------------------------------------//
		/**
		 * Event constructor
		 * 
		 * @param string $name Required to map handlers to appropriate events
		 * @param object $parent Parent object instance
		 */
		public function __construct( $name, &$parent = null )
		{
			$this->_name = $name;

			if( $parent )
			{
				$this->_parent = &$parent;
			}
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get parent object of this event
		 * 
		 * @return mixed
		 */
		public function getParent()
		{
			return $this->_parent;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get name of this event
		 * 
		 * @return string
		 */
		public function getName()
		{
			return $this->_name;
		}
	}
}