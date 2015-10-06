<?php

namespace Kalibri\Model;

class Active extends Base
{
    protected $entityClass;

//------------------------------------------------------------------------------------------------//
    public function __construct()
    {
        parent::__construct();

        $this->entityClass = $this->getEntityClassName();
    }

//------------------------------------------------------------------------------------------------//
    public function insert( $data )
    {
        try{
            $this->getQuery()->insert( $data )->execute( $this->connectName );
            $this->removeCache('all');

            return $this->db()->lastInsertId();
        } catch( \Exception $e ) {
            throw $e;
        }
    }

//------------------------------------------------------------------------------------------------//
    public function delete( $keyValue )
    {
        $this->deleteBy($this->keyField, $keyValue);
    }

//------------------------------------------------------------------------------------------------//
    public function deleteBy( $field, $value )
    {
        $this->getQuery()->delete()->where( $field, $value )
            ->execute();
    }

//------------------------------------------------------------------------------------------------//
    public function save( $data )
    {
        if( isset( $data[ $this->keyField ] ) && !$data[ $this->keyField ] )
        {
            unset( $data[ $this->keyField ] );
        }

        if( isset( $data[ $this->keyField ] ) )
        {
            $this->update( $data );

            return $data[ $this->keyField ];
        }
        else
        {
            return $this->insert( $data );
        }
    }

//------------------------------------------------------------------------------------------------//
    public function insertBatch( $data )
    {
        $fields = array_keys( $data[0] );
        $values = array();
        $i=0;

        $sql = 'INSERT INTO '.$this->tableName. '('.implode( ', ',$fields ).') VALUES ';

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
        $sql = 'UPDATE '.$this->tableName.' SET ';

        foreach( array_keys( $data[0] ) as $field )
        {
            if( $field == $this->keyField )
            {
                continue;
            }

            $sql .= $field.'=:'.$field.', ';
        }

        $stmt = $this->db()->prepare( substr( $sql, 0, -2 ). ' WHERE '.$this->keyField.'=:'.$this->keyField );

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
            $result = $this->db()->execStatment("select * from {$this->tableName} where {$this->keyField}=:id limit 1", array(
                ':id'=>$id
            ))->fetchAndClose();

            $this->setCache( $id, $result, \Kalibri\Helper\Date::SEC_IN_MINUTE*5 );
        }

        return $result;
    }

//------------------------------------------------------------------------------------------------//
    public function getBy( $id, $field = null )
    {
        $field = $field?: $this->keyField;

        return $this->db()->execStatment("select * from {$this->tableName} where $field=:id", array(
            ':id'=>$id
        ))->fetchAndClose();
    }

//------------------------------------------------------------------------------------------------//
    public function getSingleBy( $field, $id )
    {
        $field = $field?: $this->keyField;

        return $this->db()->execStatment("select * from {$this->tableName} where $field=:id limit 1", array(
            ':id'=>$id
        ))->fetchAndClose();
    }

//------------------------------------------------------------------------------------------------//
    /**
     * @todo optimize selecting and slicing
     */
    public function getAll( $count = null, $offset = null )
    {
        $query = $this->getQuery()->select();

        if( $offset || $count ) {
            $query->limit( $count, $offset );
        }

        var_dump($query->execute());exit();

        $result = $query->execute()->fetchAll();

        return $result;
    }

//------------------------------------------------------------------------------------------------//
    public function getAllBy( $field, $value )
    {
        $result = $this->getQuery()->select()->where( $field, $value )->execute()->fetchAllAndClose();

        return $result;
    }

//------------------------------------------------------------------------------------------------//
    public function count()
    {
        $result = $this->getQuery()->count( $this->keyField )->execute()->fetchAndClose();
        return (int)$result['count'];
    }

//------------------------------------------------------------------------------------------------//
    public function update( $data, $keyField = null )
    {
        $result = false;
        $keyField = $keyField ?: $this->keyField;

        if( isset( $data[ $keyField ] ) )
        {
            $keyValue = $data[ $keyField ];
            unset( $data[ $keyField ] );

            $this->getQuery()->update( $data )->where( $keyField, $keyValue)->limit(1)
                ->execute();

            $result = $this->db()->affectedRows();
        }

        return $result;
    }

//------------------------------------------------------------------------------------------------//
    public function toEntity( array $data = null )
    {
        if( $data === null )
        {
            return null;
        }

        return new $this->entityClass( $data );
    }

//------------------------------------------------------------------------------------------------//
    public function getEntityClassName()
    {
        if( !$this->entityClass )
        {
            $class = get_class( $this );
            $pos = strrpos( $class, '\\' );

            $this->entityClass = substr( $class, 0, $pos ).'\\Entity'.substr( $class, $pos );
        }

        return $this->entityClass;
    }

//------------------------------------------------------------------------------------------------//
    public function getEntity( $id, $field = null )
    {
        if( is_object( $id ) )
        {
            return $id;
        }

        if( ( $data = $this->get( (int)$id, $field ) ) )
        {
            $data = $this->toEntity( $data );
        }

        return $data;
    }

//--------------------------------------------------------------------------------------------------------------------//
    public function getLastId()
    {
        $result = $this->getQuery()
            ->select( $this->keyField )
            ->orderBy( $this->keyField, \Kalibri\Db\Query::ORDER_DIR_DESC )
            ->limit(1)
                ->execute()
                ->fetchAndClose();

        return $result? $result[$this->keyField]: null;
    }

//--------------------------------------------------------------------------------------------------------------------//
    function getAllWithPrimaryAsKey()
    {
        $result = [];
        $dbResult = $this->getAll();

        foreach($dbResult as $row)
        {
            $result[ $row[$this->keyField] ] = $row;
        }

        return $result;
    }
}