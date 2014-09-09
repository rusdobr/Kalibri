<?php

namespace Kalibri\Db\Builder;

use \Kalibri\Db\Query as Query;

class Mysql extends Base
{
    public function getSql()
    {
        switch( $this->_data['function'] )
        {
            case Query::F_SELECT:
                return 'SELECT '.$this->getFieldsPart().' FROM '.
                    $this->_data['table_name'].
                    $this->getWherePart().
                    $this->getGroupByPart().
                    $this->getOrderByPart().
                    $this->getLimitPart();
            case Query::F_INSERT:
                return 'INSERT INTO '.$this->_data['table_name'].$this->getInsertValuesPart();
            case Query::F_UPDATE:
                return 'UPDATE '.$this->_data['table_name'].' SET '.$this->getUpdateValuesPart().$this->getWherePart();
            case Query::F_DELETE:
                return 'DELETE FROM '.$this->_data['table_name'].$this->getWherePart().$this->getLimitPart();
            case Query::F_COUNT:
                return "SELECT count( {$this->_data['fields']} ) as \"count\" FROM {$this->_data['table_name']}".$this->getWherePart();
        }

        throw new \Kalibri\Exception('Empty or unsupported query constructed.');
    }
}