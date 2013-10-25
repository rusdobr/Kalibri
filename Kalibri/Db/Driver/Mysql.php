<?php

namespace Kalibri\Db\Driver {

	use \Kalibri\Db\Builder as Builder;

	class Mysql extends \Kalibri\Db\Driver\Base
	{
		/**
		 * @var \PDO
		 */
		protected $_link;

		protected $_pdo_params;

//------------------------------------------------------------------------------------------------//
		public function __construct( array $config = null )
		{
			parent::__construct( $config );

			if( isset( $this->_config['encoding'] ) )
			{
				$this->_pdo_params = array( 
					\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$this->_config['encoding']}'" 
				);
			}
		}

//------------------------------------------------------------------------------------------------//
		public function beginTransaction()
		{
			$this->connect()->beginTransaction();
		}

//------------------------------------------------------------------------------------------------//
		public function commit()
		{
			$this->connect()->commit();
		}

//------------------------------------------------------------------------------------------------//
		public function connect()
		{	
			if( $this->_link )
			{
				return $this->_link;
			}

			try
			{
				if( isset( $this->_config['dsn'] ) )
				{
					$this->_link = new \PDO( $this->_config['dsn'], $this->_config['user'], $this->_config['password'],
						( $this->_pdo_params? : array() )
					);

					$this->_link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
				}
				else
				{
					$this->_link = new \PDO("mysql:host={$this->_config['host']};dbname={$this->_config['name']};port=".
						(isset( $this->_config['port'] )? $this->_config['port']: 3306), 
							$this->_config['user'], $this->_config['password']);

					$this->_link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

					if( isset( $this->_config['encoding'] ) )
					{
						$this->_link->exec('SET NAMES '.$this->_config['encoding']);
					}
				}
			}
			catch( PDOException $e )
			{
				throw \Kalibri\Exception\Db\Connection( $e->getMessage() );
			}

			return $this->_link;
		}

//------------------------------------------------------------------------------------------------//
		public function disconnect()
		{
			$this->_link = null;
		}

//------------------------------------------------------------------------------------------------//
		public function escape( $string )
		{
			/**
			 * @todo implement escaping
			 */
			//return \mysqli_real_escape_string( $this->connect(), $string );
		}

//------------------------------------------------------------------------------------------------//
		public function query( $sql )
		{
			if( \Kalibri::config()->get('debug.log.is-enabled', false) && \Kalibri::config()->get('debug.log.collect-db-queries', false) )
			{
				\Kalibri::logger()->add(\Kalibri\Logger\Base::L_DEBUG, 'SQL query:'.$sql, $this);
			}
			
			$result = $this->connect()->query( $sql );

			if( !$result )
			{
				throw new \Kalibri\Db\Exception( $this->getErrorCode().': '.$this->getError()."\nSQL: $sql", 'Db Error' );
			}

			return new \Kalibri\Db\Result\Mysql( $result );
		}

//------------------------------------------------------------------------------------------------//
		public function exec( \Kalibri\Db\Query $query )
		{
			$builder = new Builder\Mysql( $query );
			return $this->execStatment( $builder->getSql(), $builder->getParams() );
		}

//------------------------------------------------------------------------------------------------//
		public function execStatment( $query, array $params = null )
		{
			if( \Kalibri::config()->get('debug.log.is-enabled', false) && \Kalibri::config()->get('debug.log.collect-db-queries', false) )
			{
				\Kalibri::logger()->add(\Kalibri\Logger\Base::L_DEBUG, 'SQL query:'.$query, $this);
				\Kalibri::logger()->add(\Kalibri\Logger\Base::L_DEBUG, 'SQL params:'.  var_export( $params, true), $this);
			}
			
			try
			{
				$stmt = $this->connect()->prepare( $query );
				$stmt->execute( $params );

			}
			catch( \Exception $e )
			{
				$exception = new \Kalibri\Db\Exception( $e->getMessage() );
				$exception->setQueryinfo( $query, $params );

				throw $exception;
			}

			return new \Kalibri\Db\Result\Mysql( $stmt );
		}

//------------------------------------------------------------------------------------------------//
		public function getError()
		{
			return $this->connect()->errorInfo();
		}

//------------------------------------------------------------------------------------------------//
		public function getErrorCode()
		{
			return $this->connect()->errorCode();
		}

//------------------------------------------------------------------------------------------------//
		public function isConnected()
		{
			return $this->_link instanceof \PDO;
		}

//------------------------------------------------------------------------------------------------//
		public function lastInsertId()
		{
			return $this->connect()->lastInsertId();
		}

//------------------------------------------------------------------------------------------------//
		public function prepare( $statment )
		{
			return $this->connect()->prepare( $statment );
		}

//------------------------------------------------------------------------------------------------//
		public function rollback()
		{
			$this->connect()->rollBack();
		}

//------------------------------------------------------------------------------------------------//
		public function affectedRows()
		{
		}
	}
}