<?php

namespace Kalibri\Db\Builder {
	
	class Pgsql extends Mysql 
	{
		public function getInsertValuesPart()
		{
			return parent::getInsertValuesPart().' RETURNING '.$this->_data['table_name'].'_id';
		}
	}
}
