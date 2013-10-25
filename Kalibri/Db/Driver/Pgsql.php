<?php

namespace Kalibri\Db\Driver {

	class Pgsql extends Mysql
	{
		public function __construct( array $config = null )
		{
			parent::__construct( $config );
		}
	}
}