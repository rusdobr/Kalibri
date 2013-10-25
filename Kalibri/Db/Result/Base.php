<?php

namespace Kalibri\Db\Result {

	/**
	 * Base class for database result objects
	 */
	abstract class Base
	{
		/**
		 * Symlink to query result resource
		 * @var resource
		 */
		protected $_result;

//------------------------------------------------------------------------------------------------//
		/**
		 * Base result class constructor
		 * 
		 * @param resource $result Database query result resource
		 */
		public function __construct( &$result )
		{
			$this->_result = &$result;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Fetch single record from result
		 * 
		 * @return array
		 */
		abstract public function fetch();
//------------------------------------------------------------------------------------------------//
		/**
		 * Fetch all records from result
		 * 
		 * @return array
		 */
		abstract public function fetchAll();
//------------------------------------------------------------------------------------------------//
		/**
		 * Returns amount of rows stored in this result
		 * 
		 * @return int
		 */
		abstract public function numRows();
//------------------------------------------------------------------------------------------------//
		/**
		 * Close this result resource and free memory
		 */
		abstract public function close();

//------------------------------------------------------------------------------------------------//
		public function fetchAndClose()
		{
			$data = $this->fetch();
			$this->close();
			return $data;
		}

//------------------------------------------------------------------------------------------------//
		public function fetchAllAndClose()
		{
			$data = $this->fetchAll();
			$this->close();

			return $data;
		}
	}
}