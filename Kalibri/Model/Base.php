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
		protected $tableName;

		/**
		 * @var \Kalibri\Db
		 */
		protected $_db;

		/**
		 * Assigned connection name
		 * @var string
		 */
		protected $connectName;

		protected $keyField;

		/**
		 * @var \Kalibri\Cache\Driver\Memcache
		 */
		protected $_cache = null;

		/**
		 * @var array
		 */
		protected $_cacheKeys = [];

//------------------------------------------------------------------------------------------------//
		public function __construct()
		{
			if( !$this->tableName ) {
				$this->tableName = strtolower(
					str_replace( 
						[\Kalibri::app()->getNamespace().'\\App\\Model\\', 'Kalibri\\Model\\'], 
						'', 
						static::class 
				));
			}

			$this->keyField = $this->keyField ?: $this->tableName.'_id';
			$this->_cache = \Kalibri::cache();

			// Register model
			\Kalibri::model( $this->tableName, $this );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get DB connection instance
		 * 
		 * @return \Kalibri\Db\Driver\Mysql
		 */
		protected function db()
		{
			if( !$this->_db ) {
				$this->_db = \Kalibri::db()->getConnection( $this->connectName );
			}

			return $this->_db;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return \Kalibri\Db\Query
		 */
		public function getQuery()
		{
			return $this->db()->getQuery()->from( $this->tableName )
					->setConnectionName( $this->connectName );
		}

//------------------------------------------------------------------------------------------------//
		public function getCache( $key )
		{
			return $this->_cache->get( $this->tableName.$key );
		}

//------------------------------------------------------------------------------------------------//
		public function setCache( $key, $value, $expire = 0 )
		{
			return $this->_cache->set( $this->tableName.$key, $value, $expire );
		}

//------------------------------------------------------------------------------------------------//
		public function removeCache( $key )
		{
			return $this->_cache->remove( $this->tableName.$key );
		}

//------------------------------------------------------------------------------------------------//
		public function flushCache( $params = null ): void
		{
			$this->_cache->remove( $this->tableName.'all' );

			if( is_array( $params ) && count( $this->_cacheKeys ) )
			{
				foreach( $this->_cacheKeys as $key )
				{
					foreach( $params as $param=>$value )
					{
						$key = str_replace( $param, $value, (string) $key );
					}

					$this->_cache->remove( $this->tableName.$key );
				}
			}
			else
			{
				$this->_cache->remove( $this->tableName.$params );
			}
		}

//------------------------------------------------------------------------------------------------//
		public function getKeyFieldName()
		{
			return $this->keyField;
		}
	}
}