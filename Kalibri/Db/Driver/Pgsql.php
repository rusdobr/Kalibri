<?php

namespace Kalibri\Db\Driver {

	use \Kalibri\Db\Builder as Builder;
	
	class Pgsql extends Mysql
	{
		public function __construct( array $config = null )
		{
			parent::__construct( $config );
		}
		
//------------------------------------------------------------------------------------------------//
		public function exec( \Kalibri\Db\Query $query )
		{
			$builder = new Builder\Pgsql( $query );
			return $this->execStatment( $builder->getSql(), $builder->getParams() );
		}
	}
}