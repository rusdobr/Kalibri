<?php

namespace Kalibri\Model {

	class User extends \Kalibri\Model\Active
	{
		protected $_tableName = 'user';
		protected $_keyField = 'user_id';
		
//------------------------------------------------------------------------------------------------//
		public function getByLogin( $login )
		{
			return $this->getQuery()->select()->where( 'login', $login )
				->limit(1)->execute()->fetch();
		}

//------------------------------------------------------------------------------------------------//
		public function register( $data )
		{
			if( isset( $data['login'], $data['password'] ) )
			{
				$this->insert( $data );
				return true;
			}

			return false;
		}
	}
}