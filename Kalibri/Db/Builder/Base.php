<?php

namespace Kalibri\Db\Builder {

	abstract class Base
	{
		/**
		* @var array
		*/
		protected $_data = NULL;
		protected $_params = array();

//------------------------------------------------------------------------------------------------//
		public function  __construct( $data = NULL )
		{
			$this->init( $data );
		}

//------------------------------------------------------------------------------------------------//
		public function init( $data )
		{
			if( \is_array( $data ) )
			{
				$this->_data = $data;
			}
			elseif( $data instanceof \Kalibri\Db\Query )
			{
				$this->_data = $data->getData();
			}
			elseif( $data instanceof \Kalibri\Db\Builder\Base )
			{
				$this->_data = $data->getData();
			}
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return &array
		 */
		public function getData()
		{
			return $this->_data;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return string
		 */
		abstract public function getSql();

//------------------------------------------------------------------------------------------------//
		/**
		 * @return string
		 */
		public function getFieldsPart()
		{
			$fields = '';
			foreach( $this->_data['fields'] as $as => $field )
			{
				if( !empty( $fields ) )
				{
					$fields .= ', ';
				}

				if( $field[0] == '&' )
				{
					$field = substr( $field, 1);
				}

				if( !is_numeric( $as ) )
				{
					$fields .= $field.' as '.$as;
				}
				else
				{
					$fields .= $field;
				}
			}

			return $fields;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return string
		 */
		public function getInsertValuesPart()
		{	
			$fields = array();
			$values = array();

			foreach( $this->_data['fields'] as $field=>$value )
			{
				$fields[] = "$field";
				if( !strlen( $value ) || $value[0] !== '&' )
				{
					$values[] = ':'.$field;
				}
				else
				{
					$values[] = $value;
				}

			}

			return ' ('.implode(', ', $fields).') VALUES('.implode(', ', $values).')';
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return string
		 */
		public function getUpdateValuesPart()
		{
			$values = array();

			foreach( $this->_data['fields'] as $field=>$value )
			{
				if( !strlen( $value ) || $value[0] !== '&' )
				{
					$values[] = "$field=:$field";
				}
				else
				{
					$values[] = $field.'='.$value;
				}
			}

			return implode( ', ', $values );
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return string
		 */
		public function getWherePart()
		{
			$where = '';
			if( \count( $this->_data['where'] ) > 0 )
			{
				$where = ' WHERE ';
				foreach( $this->_data['where'] as $value )
				{
					//Group with specified compare operation
					if( \is_array( $value ) && isset( $value['field'] ) && isset( $value['op'] ) )
					{
						$key = $this->addParam( $value['field'] , $value['value'] );
						$where .="( {$value['field']} {$value['op']} {$key} ) ";
					}
					//Field=>Value
					elseif( \is_array($value) && isset( $value['field'] ) )
					{
						$key = $this->addParam( $value['field'] , $value['value'] );
						$where .= " {$value['field']} = {$key} " ;
					}
					elseif( \is_array( $value ) && \count( $value ) > 0 )
					{
						$where .= '(';
						foreach( $value as $val )
						{
							//Group with specified compare operation
							if( \is_array( $val ) && isset( $val['field'] ) && isset( $val['op'] ) )
							{
								$key = $this->addParam( $val['field'] , $val['value'] );
								$where .="( {$val['field']} {$val['op']} {$key} ) ";
							}
							//Field=>Value
							elseif( \is_array($val) && isset( $val['field'] ) )
							{
								$key = $this->addParam( $val['field'] , $val['value'] );
								$where .= " {$val['field']} = {$key} " ;
							}
							// Grouping operation like: and, or
							elseif( \is_string( $val ) )
							{
								$where .= $val;
							}
						}
						$where .= ')';
					}
					//Grouping operation like: and, or
					elseif( \is_string( $value ) )
					{
						$where .= $value;
					}
				}
			}

			return $where;
		}

//------------------------------------------------------------------------------------------------//
		protected function addParam( $key, $value )
		{
			if( isset( $this->_params[ $key ] ) ) {
				$key .= '_'.mt_rand(0, 1000);
				return $this->addParam( $key, $value );
			}

			$key = ':'.$key;
			$this->_params[ $key ] = $value;
			return $key;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return string
		 */
		public function getLimitPart()
		{
			if( \is_array( $this->_data['limit'] ) )
			{
				$limit = ' LIMIT ';
				if( $this->_data['limit']['offset'] )
				{
					$limit .= $this->_data['limit']['count'].' OFFSET '.$this->_data['limit']['offset'];
				}
				else
				{
					$limit .= $this->_data['limit']['count'];
				}

				return $limit;
			}

			return '';
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return string
		 */
		public function getOrderByPart()
		{
			$order_by = '';
			if( \is_array( $this->_data['order_by'] ) && \count( $this->_data['order_by'] ) > 0 )
			{
				$order_by = ' ORDER BY '.$this->_data['order_by']['field'].' '.
						\strtoupper( $this->_data['order_by']['dir'] );
			}

			return $order_by;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return string
		 */
		public function getGroupByPart()
		{
			$group_by = '';
			if( \is_array( $this->_data['group_by'] ) && \count( $this->_data['group_by'] ) > 0 )
			{
				foreach( $this->_data['group_by'] as $field )
				{
					if( !empty( $group_by ) )
					{
						$group_by .=', ';
					}

					$group_by .= $field;
				}

				$group_by = ' GROUP BY '.$group_by;
			}

			return $group_by;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @return string
		 */
		public function getJoinPart()
		{
			/**
			 * @todo Implement JOIN
			 */
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get class as string will return current sql statment
		 *
		 * @return string
		 */
		public function __toString()
		{
			return $this->getSql();
		}

//------------------------------------------------------------------------------------------------//
		protected function _escapeAll()
		{
			/**
			 * @todo Escape all in sql builder
			 */
			/*$arrays = array('group_by', 'fields');

			for( $i=0; $i< count( $arrays ); $i++ )
			{
				if( is_array( $this->_data[ $arrays[$i] ] ) )
				{
					foreach( $this->_data[ $arrays[$i] ] as $id=>$field )
					{
						if( $field !== '*' )
						{
							$this->_data[ $arrays[$i] ][ $id ] = "$field";
						}
					}
				}
			}*/
		}

//------------------------------------------------------------------------------------------------//
		public function getParams()
		{
			$params = array();

			if( is_array( $this->_data['fields'] ) )
			{
				foreach( $this->_data['fields'] as $field=>$value )
				{
					if( $value[0] !== '&' && $value !== '*' && !is_numeric( $field ) )
					{
						$params[ ':'.$field ] = $value;
					}
				}
			}

			return array_merge( $params, $this->_params );
		}
	}
}