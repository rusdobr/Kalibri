<?php

namespace Kalibri\Db\Driver {

	abstract class Base
	{
		/**
		 * Databse connection link resource
		 */
		protected $_link = null;
		protected $_config = null;
		protected $_builderClass;

		public function __construct( array $config = null )
		{
			if( \is_array( $config ) )
			{
				$this->_config = $config;
			}
		}

		abstract public function connect();
		abstract public function disconnect();

		/**
		 * @return Kalibri\Db\Result\Base
		 */
		abstract public function query( $sql );

		/**
		 * @param \Kalibri\Db\Query $query
		 * 
		 * @return Kalibri\Db\Result\Base
		 */
		abstract public function exec( \Kalibri\Db\Query $query );

		/**
		 * @return Kalibri\Db\Result\Base
		 */
		abstract public function execStatment( $query, array $params = null );

		abstract public function lastInsertId();
		abstract public function prepare( $statment );
		abstract public function beginTransaction();
		abstract public function rollback();
		abstract public function commit();
		abstract public function isConnected();
		abstract public function getErrorCode();
		abstract public function getError();
		abstract public function escape( $string );
		abstract public function affectedRows();

		/**
		 * @return \Kalibri\Db\Builder\Mysql
		 */
		public function getBuilder()
		{
			if( !$this->_builderClass )
			{
				$explodedName = explode( '\\', get_class( $this ) );

				$this->_builderClass = '\\Kalibri\\Db\\Builder\\'.end( $explodedName );

				if( !class_exists($this->_builderClass, true) )
				{
					throw new \Kalibri\Db\Exception('Query builder for driver '.get_class( $this ).' not found');
				}
			}

			return new $this->_builderClass();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Create instance of Query bulder
		 * 
		 * @return Kalibri\Db\Query
		 */
		public function getQuery()
		{
			return new \Kalibri\Db\Query();
		}
	}
}