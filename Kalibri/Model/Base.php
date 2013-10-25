<?php

namespace Kalibri\Model {

	/**
	 * Description of Base
	 *
	 * @package Kalibri
	 * @subpackage Model
	 * 
	 * @author Alexander Kostinenko aka tenebras
	 */
	class Base
	{
		/**
		 * @var string $tableName
		 */
		protected $_tableName;

		/**
		 * @var \Kalibri\Db
		 */
		protected $_db;

		/**
		 * Assigned connection name
		 * @var string
		 */
		protected $_connectName;

		protected $_keyField;

		/**
		 * @var \Kalibri\Cache
		 */
		protected $_cache = null;

		/**
		 * @var array
		 */
		protected $_cacheKeys = array();

//------------------------------------------------------------------------------------------------//
		public function __construct()
		{
			if( !$this->_tableName )
			{
				$this->_tableName = strtolower( 
					str_replace( 
						array( \Kalibri::app()->getNamespace().'\\App\\Model\\', 'Kalibri\\Model\\' ), 
						'', 
						get_class( $this ) 
				));
			}

			$this->_keyField = $this->_keyField ?: $this->_tableName.'_id';
			$this->_cache = \Kalibri::cache();

			// Register model
			\Kalibri::model( $this->_tableName, $this );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get DB connection instance
		 * 
		 * @return \Kalibri\Db\Driver\Mysql
		 */
		protected function db()
		{
			if( !$this->_db )
			{
				$this->_db = \Kalibri::db()->getConnection( $this->_connectName );
			}

			return $this->_db;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return \Kalibri\Db\Query
		 */
		public function getQuery()
		{
			return $this->db()->getQuery()->from( $this->_tableName )
					->setConnectionName( $this->_connectName );
		}

//------------------------------------------------------------------------------------------------//
		public function getCache( $key )
		{
			return $this->_cache->get( $this->_tableName.$key );
		}

//------------------------------------------------------------------------------------------------//
		public function setCache( $key, $value, $expire = 0 )
		{
			return $this->_cache->set( $this->_tableName.$key, $value, $expire );
		}

//------------------------------------------------------------------------------------------------//
		public function removeCache( $key )
		{
			return $this->_cache->remove( $this->_tableName.$key );
		}

//------------------------------------------------------------------------------------------------//
		public function flushCache( $params = null )
		{
			$this->_cache->remove( $this->_tableName.'all' );

			if( is_array( $params ) && count( $this->_cacheKeys ) )
			{
				foreach( $this->_cacheKeys as $key )
				{
					foreach( $params as $param=>$value )
					{
						$key = str_replace( $param, $value, $key );
					}

					$this->_cache->remove( $this->_tableName.$key );
				}
			}
			else
			{
				$this->_cache->remove( $this->_tableName.$params );
			}
		}
	}
}