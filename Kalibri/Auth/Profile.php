<?php

namespace Kalibri\Auth{

	class Profile
	{
		const ROLE_GENERAL = 0;
		const ROLE_ADMIN = 1;

		protected $_data = array();
		protected $_changedFields = array();
		
//------------------------------------------------------------------------------------------------//
		public function __construct( array $data = null )
		{
			$this->_data = $data;
		}

//------------------------------------------------------------------------------------------------//
		public function __get( $key )
		{
			return isset( $this->_data[ $key ] )? $this->_data[ $key ]: null;
		}

//------------------------------------------------------------------------------------------------//
		public function __set( $key, $value )
		{
			$this->_data[ $key ] = $value;
		}
		
//------------------------------------------------------------------------------------------------//
		public function getData()
		{
			return $this->_data;
		}
		
//------------------------------------------------------------------------------------------------//
		public function getSaveData()
		{
			$data = array();

			if( count( $this->_changedFields ) && $this->user_id )
			{
				$data['user_id'] = $this->user_id;

				foreach( $this->_changedFields as $field )
				{
					$data[ $field ] = $this->_data[ $field ];
				}
			}

			return $data;
		}

//------------------------------------------------------------------------------------------------//
		public function save()
		{
			\Kalibri::model('user')->save( $this->getSaveData() );
		}
	}
}