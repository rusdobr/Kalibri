<?php

namespace Kalibri\Db {

	/**
	 * @package Kalibri
	 * @subpackage Db
	 */
	class Query
	{
		public const FIELD_ANY = '*';
		public const ORDER_DIR_ASC = 'asc';
		public const ORDER_DIR_DESC = 'desc';

		public const OP_AND = 'and';
		public const OP_OR = 'or';
		public const OP_NOT = 'not';
		public const OP_LARGER = '>';
		public const OP_SMALLER = '<';
		public const OP_LARGER_OR_EQUAL = '>=';
		public const OP_SMALLER_OR_EQUAL = '<=';

		public const F_UPDATE = 'update';
		public const F_SELECT = 'select';
		public const F_INSERT = 'insert';
		public const F_DELETE = 'delete';
		public const F_COUNT  = 'count';
		public const F_RENAME = 'rename';

		/**
		 * @var array
		 */
		protected $_data;

		/**
		 * @var string
		 */
		protected $_connectName;

		protected $_usePrefix = true;

//------------------------------------------------------------------------------------------------//
		public function __construct()
		{
			$this->_data = ['limit'=>NULL, 'table_name'=>'', 'fields'=>[], 'function'=>'', 'where'=>[], 'join'=>[], 'order_by'=>[], 'group_by'=>[]];

			$this->_connectName = \Kalibri::config()->get('db.default');
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @param array|string $fields
         * @param array|string $asField
         *
         * @return \Kalibri\Db\Query
         */
        public function &select( $fields = self::FIELD_ANY, $asField = '' )
		{
			$this->_data['function'] = self::F_SELECT;

			if( $fields == self::FIELD_ANY )
			{
				$this->_data['fields'][] = $fields;

				return $this;
			}
			elseif( \is_string( $fields ) )
			{
				if( \strlen( $asField ) )
				{
					// Use $fields as $asField
					$this->_data['fields'][ $asField ] = $fields;
				}
				else
				{
					$this->_data['fields'][] = $fields;
				}

				return $this;
			}
			elseif( \is_array( $fields ) )
			{
				foreach( $fields as $field )
				{
					if( \is_array( $asField ) && isset( $asField[ $field ] ) && !empty ( $asField[ $field ] ) )
					{
						$this->_data['fields'][ $asField[ $field ] ] = $field;
					}
					else
					{
						$this->_data['fields'][] = $field;
					}
				}

				return $this;
			}

			throw new \Kalibri\Exception\Invalid\Expression();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Update query
		 * 
		 * @param array $data
		 * @param string $tableName
		 * 
		 * @return \Kalibri\Db\Query
		 */
		public function &update( $data, $tableName = null )
		{
			$this->_data['function'] = self::F_UPDATE;
			$this->_data['fields'] = $data;

			if( $tableName )
			{
				$this->_setTableName( $tableName );
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Insert row into DB
		 * 
		 * @param array $data
		 * @param string $tableName
		 * 
		 * @return \Kalibri\Db\Query
		 */
		public function &insert( $data, $tableName = null )
		{
			$this->_data['function'] = self::F_INSERT;
			$this->_data['fields'] = $data;

			if( $tableName )
			{
				$this->_setTableName( $tableName );
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Delete query. To set items to delete use where() and limit() or table will be fully cleared. 
		 * 
		 * @return \Kalibri\Db\Query
		 */
		public function &delete()
		{
			$this->_data['function'] = self::F_DELETE;

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Re-name table query
		 * 
		 * @todo Rename table with prefix
		 * 
		 * @param string $oldName
		 * @param string $newName
		 * 
		 * @return \Kalibri\Db\Query
		 */
		public function &rename( $oldName, $newName )
		{
			$this->_data['function'] = 'rename';
			$this->_data['table_name'] = [$oldName => $newName];

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Add from part to query
		 * 
		 * @param string $tableName Table name
		 * @param string $asTable Alias name for table
		 * 
		 * @return \Kalibri\Db\Query
		 */
		public function from( $tableName, $asTable = null )
		{
			$this->_setTableName( $tableName, $asTable );

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		public function where( $fields, $value = NULL, $operation = '' )
		{
			if( \is_array( $fields ) )
			{
				$operation = !empty($value)? $value: 'and';

				$group = \count( $this->_data['where'] );
				$this->_data['where'][ $group ] = [];

				foreach( $fields as $fieldName=>$_value )
				{
					if( \is_array( $_value ) && \count( $_value ) == 2 )
					{
						$this->_data['where'][$group][] = ['field'=>$fieldName, 'op'=>$_value[1], 'value'=>$_value[0]];
					}
					elseif( !\is_array( $_value ) )
					{
						$this->_data['where'][$group][] = ['field'=>$fieldName, 'op'=>'=', 'value'=>$_value];
					}

					$this->_data['where'][$group][] = $operation;
				}
				//remove last operation
				unset( $this->_data['where'][$group][ \count($this->_data['where'][$group])-1 ] );
			}
			else
			{
				$operation = !empty( $operation )? $operation: '=';
				$this->_data['where'][] = ['field'=>$fields, 'op'=>$operation, 'value'=>$value];
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
        /**
         * Add operation to where statement. This function helps construct complicated statements.
         *
         * @param string $operation
         *
         * @throws \Kalibri\Exception\Invalid\Expression
         *
         * @return \Kalibri\Db\Query
         */
		public function add( $operation )
		{
			$operation = \strtolower( $operation );

			if( \in_array( $operation, [self::OP_OR, self::OP_AND, self::OP_NOT] ) )
			{
				$this->_data['where'][] = $operation;

				return $this;
			}

			throw new \Kalibri\Exception\Invalid\Expression();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Add limit expression to query
		 * 
		 * @param int $count Records count limit
		 * @param int $offset Result offset
		 *
		 * @return \Kalibri\Db\Query
		 */
		public function limit( $count, $offset = null  )
		{	
			$this->_data['limit'] = ['offset'=> $offset ?: 0, 'count'=> $count ?: 1];

			return $this;
		}

//------------------------------------------------------------------------------------------------//
        /**
         * Add order by expression to query
         *
         * @param string $field Field name
         * @param string $direction Order direction
         *
         * @throws \Kalibri\Exception\Invalid\Param
         *
         * @return \Kalibri\Db\Query
         */
		public function orderBy( $field, $direction = self::ORDER_DIR_ASC )
		{
			if( $direction != self::ORDER_DIR_ASC && $direction != self::ORDER_DIR_DESC )
			{
				throw new \Kalibri\Exception\Invalid\Param("Invalid direction in order statement '$direction'");
			}

			$this->_data['order_by'] = ['field'=>$field, 'dir'=>$direction];

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Add group by expression
		 * 
		 * @param mixed(array,string) $field Fields list or name
		 * 
		 * @return \Kalibri\Db\Query
		 */
		public function groupBy( $field )
		{
			if( is_array( $field ) )
			{
				$this->_data['group_by'] = $field;
			}
			else
			{
				$this->_data['group_by'][] = $field;
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get all query specific data
		 * 
		 * @return array
		 */
		public function getData()
		{
			return $this->_data;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get name of assigned function to call
		 * 
		 * @return string
		 */
		public function getFunction()
		{
			return $this->_data['function'];
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Add count function
		 * 
		 * @param string $tableName
		 * @param string $fieldName Single field name that will be counted
		 * 
		 * @return \Kalibri\Db\Query
		 */
		public function count( $fieldName = self::FIELD_ANY, $tableName = null )
		{
			$this->_data['function'] = self::F_COUNT;
			$this->_data['fields'] = (string) $fieldName;

			if( $tableName )
			{
				$this->_data['table_name'] = $this->_setTableName( $tableName );
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Execute query on given connection
		 * 
		 * @param string $dbConnectName DB Connection name
		 * 
		 * @return \Kalibri\Db\Result\Base
		 */
		public function execute( $dbConnectName = null )
		{
			if( !$dbConnectName && $this->_connectName )
			{
				$dbConnectName = $this->_connectName;
			}

			return \Kalibri::db()->getConnection( $dbConnectName )->exec( $this );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Set active connection name
		 * 
		 * @param string $name DB Connection name
		 * 
		 * @return \Kalibri\Db\Query
		 */
		public function setConnectionName( $name )
		{
			$this->_connectName = $name;
			return $this;
		}

//------------------------------------------------------------------------------------------------//
		protected function _setTableName( $tableName, $alias = null )
		{
			if( $this->usePrefix() )
			{
				$tableName = \Kalibri::config()
						->get("db.connections.{$this->_connectName}.table-prefix", '').$tableName;
			}

			if( $alias )
			{
				$this->_data['table_name'] = [$alias => $tableName];
				return;
			}

			$this->_data['table_name'] = $tableName;
		}

//------------------------------------------------------------------------------------------------//
		public function usePrefix( $mode = null )
		{
			if( $mode !== null )
			{
				$this->_usePrefix = (bool) $mode;
			}

			return $this->_usePrefix;
		}
	}
}