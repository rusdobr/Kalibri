<?php

namespace Kalibri\Db\Driver;

use \Kalibri\Db\Builder as Builder;

class Pgsql extends Mysql
{
    protected $_lastInsertId;

//------------------------------------------------------------------------------------------------//
    public function __construct( array $config = null )
    {
        parent::__construct( $config );
    }

//------------------------------------------------------------------------------------------------//
    public function exec( \Kalibri\Db\Query $query )
    {
        $builder = new Builder\Pgsql( $query );

        $result = $this->execStatment( $builder->getSql(), $builder->getParams() );

        $data = $query->getData();

        if($data['function'] ==  'insert')
        {
            $row = $result->fetch();
            if(isset( $row[ $data['table_name'].'_id' ] ))
            {
                $this->_lastInsertId = $row[ $data['table_name'].'_id' ];
            }
        }

        return $result;
    }

//------------------------------------------------------------------------------------------------//
    public function lastInsertId()
    {
        return $this->_lastInsertId;;
    }
}