<?php

namespace Kalibri\Model {

	class Active extends \Kalibri\Model\Base
	{

		protected $_entityClass;
		
//------------------------------------------------------------------------------------------------//
		public function insert( $data )
		{
			try{
				$this->getQuery()->insert( $data )->execute( $this->_connectName );
				$this->removeCache('all');
				
				return true;//return $this->db()->lastInsertId();
			} catch( \Exception $e ) {
				throw $e;
			}
			
			return false;
		}

//------------------------------------------------------------------------------------------------//
		public function delete( $keyValue )
		{
			$this->deleteBy($this->_keyField, $keyValue);
		}

//------------------------------------------------------------------------------------------------//
		public function deleteBy( $field, $value )
		{
			$this->getQuery()->delete()->where( $field, $value )
				->execute();
			
			if( $field == $this->_keyField )
			{
				$this->removeCache( $value );
			}
			
			$this->removeCache('all');
		}
		
//------------------------------------------------------------------------------------------------//
		public function save( $data )
		{
			$this->removeCache('all');
			
			if( isset( $data[ $this->_keyField ] ) )
			{
				$this->update( $data );

				return $data[ $this->_keyField ];
			}
			else
			{
				return $this->insert( $data );
			}
		}
		
//------------------------------------------------------------------------------------------------//
		public function insertBatch( $data )
		{
			$this->removeCache('all');
			
			$fields = array_keys( $data[0] );
			$values = array();
			$i=0;
			
			$sql = 'INSERT INTO '.$this->_tableName. '('.implode( ', ',$fields ).') VALUES ';
			
			foreach( $data as $item )
			{
				$i++;
				$sql .='(';
				foreach( $item as $field=>$value ){
					$sql .= ':'.$field.$i.',';
					$values[':'.$field.$i] = $value;
				}
				
				$sql = substr( $sql, 0, -1 ). '),';
			}
			
			$this->db()->execStatment( substr( $sql, 0, -1), $values );
		}
		
//------------------------------------------------------------------------------------------------//
		public function updateBatch( $data, $withTransaction = true )
		{
			$this->removeCache('all');
			
			$sql = 'UPDATE '.$this->_tableName.' SET ';
			
			foreach( array_keys( $data[0] ) as $field )
			{
				if( $field == $this->_keyField )
				{
					continue;
				}
				
				$sql .= $field.'=:'.$field.', ';
			}
			
			$stmt = $this->db()->prepare( substr( $sql, 0, -2 ). ' WHERE '.$this->_keyField.'=:'.$this->_keyField );
			
			if( $withTransaction )
			{
				$this->db()->beginTransaction();
			}
			
			foreach( $data as $record )
			{
				$params = array();
				foreach( $record as $field=>$value )
				{
					$params[':'.$field] = $value;
				}
				
				$stmt->execute( $params );
			}
			
			if( $withTransaction )
			{
				$this->db()->commit();
			}
		}
		
//------------------------------------------------------------------------------------------------//
		public function get( $id )
		{
			if( ( $result = $this->getCache( $id ) ) === null )
			{
				$result = $this->db()->execStatment("select * from {$this->_tableName} where {$this->_keyField}=:id limit 1", array(
					':id'=>$id
				))->fetchAndClose();

				$this->setCache( $id, $result, \Kalibri\Helper\Date::SEC_IN_MINUTE*5 );
			}

			return $result;
		}
		
//------------------------------------------------------------------------------------------------//
		public function getBy( $id, $field = null )
		{
			$field = $field?: $this->_keyField;

			$result = null;

			if( $field == $this->_keyField )
			{
				$result = $this->getCache( $id );
			}

			if( $result === null )
			{
				$result = $this->db()->execStatment("select * from {$this->_tableName} where $field=:id limit 1", array(
					':id'=>$id
				))->fetchAndClose();

				if( $field == $this->_keyField )
				{
					$this->setCache( $id, $result );
				}
			}

			return $result;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @todo optimize selecting and slicing
		 */
		public function getAll( $count = null, $offset = null )
		{
			$result = null;

			if( $count == null && $offset == null ){
				$result = $this->getCache('all');
			}

			if( $result === null )
			{
				$query = $this->getQuery()->select();

				if( $offset || $count ) {
					$query->limit( $count, $offset );
				}

				$result = $query->execute()->fetchAll();

				if( $count == null && $offset == null ){
					$this->setCache('all', $result);
				}
			}

			return $result;
		}

//------------------------------------------------------------------------------------------------//
		public function getAllBy( $field, $value )
		{
			$result = $this->getQuery()->select()->where( $field, $value )->execute()->fetchAll();

			return $result;
		}

//------------------------------------------------------------------------------------------------//
		public function count()
		{
			$result = $this->getQuery()->count( $this->_keyField )->execute()->fetchAndClose();
			return (int)$result['count'];
		}

//------------------------------------------------------------------------------------------------//
		public function update( $data, $keyField = null )
		{
			$result = false;

			$keyField = $keyField ?: $this->_keyField;

			if( isset( $data[ $keyField ] ) )
			{
				$keyValue = $data[ $keyField ];
				unset( $data[ $keyField ] );

				$this->getQuery()->update( $data )->where( $keyField, $keyValue)->limit(1)
					->execute();

				$result = $this->db()->affectedRows();

				$this->removeCache( $keyValue );
				$this->removeCache('all');
			}

			return $result;
		}

//------------------------------------------------------------------------------------------------//
		public function toEntity( array $data )
		{
			if( !$this->_entityClass )
			{
				$class = get_class( $this );
				$pos = strrpos( $class, '\\' );

				$this->_entityClass = substr( $class, 0, $pos ).'\\Entity'.substr( $class, $pos );
			}

			return new $this->_entityClass( $data );
		}

//------------------------------------------------------------------------------------------------//
		public function getEntity( $id, $field = null )
		{
			if( ( $data = $this->get( (int)$id, $field ) ) )
			{
				$data = $this->toEntity( $data );
			}

			return $data;
		}
		
//------------------------------------------------------------------------------------------------//
		public function getLastId()
		{
			$result = $this->getQuery()
				->select( $this->_keyField )
				->orderBy( $this->_keyField, \Kalibri\Db\Query::ORDER_DIR_DESC )
				->limit(1)->execute()->fetchAndClose();

			return $result? $result[$this->_keyField]: null;
		}
	}
}