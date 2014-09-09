<?php

namespace Kalibri\View {

	/**
	 * @package Kalibri
	 * @subpackage View
	 */
	class Data
	{
		/**
		 * Stored data
		 * @var array
		 */
		protected $_data = array();

//------------------------------------------------------------------------------------------------//
        /**
         * Set single value in storage
         *
         * @param string $key Key name
         * @param mixed $value Value to fill with
         *
         * @return $this
         */
		public function set( $key, $value )
		{
			$this->_data[ $key ] = $value;
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get single value from storage
		 * 
		 * @param string $key Key name to find
		 * @param mixed $default Default value that will be returned if key is not found
		 * 
		 * @return mixed
		 */
		public function get( $key, $default = null )
		{
			return isset( $this->_data[ $key ] )? $this->_data[ $key ]: $default;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Merge current view data with new one
		 * 
		 * @param array $data
		 * 
		 * @return \Kalibri\View\Data 
		 */
		public function merge( array $data )
		{
			$this->_data = \array_merge( $this->_data, $data );
			return $this;	
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get global view data
		 * 
		 * @return array
		 */
		public function &getData()
		{
			return $this->_data;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Set or reset global view data
		 * 
		 * @param array $data Data to set
		 * 
		 * @return \Kalibri\View\Data
		 */
		public function setData( array $data )
		{
			$this->_data = $data;

			return $this;
		}
	}
}