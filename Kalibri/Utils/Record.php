<?php

namespace Kalibri\Utils {
	
	/**
	 * Record represents class with runtime created getters and setters
	 * Note: IS fields must starts with '_', method added for boolean fields.
	 *
	 * Example of use:
	 * <code>
	 * class DR extends Record
	 * {
	 *	function init( $data = NULL )
	 *	{
	 *		$this->_fields = array('name'=>'', '_enabled'=>'');
	 *	}
	 * }
	 *
	 * $dr = new DR();
	 * $dr->getName();
	 * $dr->setName( $newName );
	 * $dr->isEnabled();
	 * $dr->isEnabled( $newValue );
	 * $dr->name;
	 * $dr->_enabled;
	 * $dr->get_enabled();
	 * </code>
	 *
	 * @package Kalibri
	 * @subpackage Utils
	 * @version 0.2
	 *
	 * @author <a href="mailto:kostinenko@gmail.com">Alexander Kostinenko</a>
	 */
	class Record
	{
//------------------------------------------------------------------------------------------------//
		public function __construct( $data = NULL )
		{
			$this->initData( $data );
		}
		
//------------------------------------------------------------------------------------------------//
		/**
		 * Init function must initialize all available fields.
		 * Setter will not create new field!
		 *
		 * @param array $data
		 */
		public function initData( $data = NULL )
		{
			if( is_array( $data ) )
			{
				foreach( $data as $key=>$value )
				{
					$this->$key = $value;
				}
			}
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Called on each method request. Will try to find appropriete action for field.
		 *
		 * @param string $name Method name
		 * @param string $arguments Arguments passed to method
		 *
		 * @return mixed
		 */
		public function __call( $name,  $arguments )
		{
			if( \method_exists( $this, $name ) && \is_callable( array( $this, $name ) ) )
			{
				return \call_user_func( array( &$this, $name ), $arguments );
			}

			$type = '';
			$fieldName = '';

			if( \strpos( $name, 'get') === 0 || \strpos($name, 'set') === 0 )
			{
				$type = \substr( $name, 0, 3 );
				$fieldName = \substr( $name, 3 );
				$fieldName = \strtolower( $fieldName[0] ).substr( $fieldName, 1 );
			}
			elseif( \strpos( $name, 'is' ) === 0 )
			{
				$type = 'is';
				$fieldName = \substr( $name, 2 );
				$fieldName = \strtolower( $fieldName[0] ).substr( $fieldName, 1 );
			}

			if( property_exists( $this, $fieldName ) )
			{
				switch( $type )
				{
					case 'is' :
						return $this->__is( $fieldName, current( $arguments ) );
					case 'get':
						return $this->__get( $fieldName );

					case 'set':
						return $this->__set( $fieldName, current( $arguments ) );
				}
			}
			else
			{
				\Kalibri::error()->show( 'Method not available: '.get_class($this).'::'.$name );
			}

			return null;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Method add ability to get fields like class property.
		 *
		 * @param string $name Field name
		 *
		 * @return mixed
		 */
		public function __get( $name )
		{
			return isset( $this->$name )? $this->$name: null;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Method add ability to set fields like class property.
		 *
		 * @param string $name Field name
		 * @param mixed $value New field value
		 *
		 * @return mixed
		 */
		public function __set( $name, $value )
		{
			if( isset( $this->$name ) )
			{
				$this->$name = $value;
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * 'Is' method processor
		 * Note: Internal use only!
		 *
		 * @param string $name
		 * @param bool $newValue
		 *
		 * @return bool
		 */
		protected function __is( $name, $newValue = null )
		{
			if( $newValue !== null )
			{
				$this->$name = (bool) $newValue;
			}

			return $this->$name;
		}

//------------------------------------------------------------------------------------------------//
		public function getData()
		{
			return get_class_vars( $this );
		}
	}
}