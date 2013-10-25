<?php

namespace Kalibri\Db\Result {

	class Mysql extends \Kalibri\Db\Result\Base
	{
		/**
		 * @var \PDOStatement
		 */
		protected $_result;

//------------------------------------------------------------------------------------------------//
		/**
		 * @see \Kalibri\Db\Result\Base::fetch()
		 */
		public function fetch()
		{
			return $this->_result->fetch( \PDO::FETCH_ASSOC );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @see \Kalibri\Db\Result\Base::fetchAll()
		 */
		public function fetchAll()
		{
			return $this->_result->fetchAll( \PDO::FETCH_ASSOC );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @see \Kalibri\Db\Result\Base::numRows()
		 */
		public function numRows()
		{
			return $this->_result->rowCount();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @see \Kalibri\Db\Result\Base::close()
		 */
		public function close()
		{
			$this->_result->closeCursor();
		}
	}
}