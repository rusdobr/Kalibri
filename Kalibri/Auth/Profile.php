<?php

namespace Kalibri\Auth{

	class Profile
	{
		public const ROLE_GENERAL = 0;
		public const ROLE_ADMIN = 1;

		protected $_data = [];
		protected $_changedFields = [];
		
//------------------------------------------------------------------------------------------------//
		public function __construct( array $data = null )
		{
			$this->_data = $data;
		}

//------------------------------------------------------------------------------------------------//
		public function __get( $key )
		{
			return $this->_data[ $key ] ?? null;
		}

//------------------------------------------------------------------------------------------------//
		public function __set( $key, $value )
		{
			$this->_data[ $key ] = $value;
			$this->notifyChanged( $key );
		}
		
//------------------------------------------------------------------------------------------------//
		public function getData()
		{
			return $this->_data;
		}
		
//------------------------------------------------------------------------------------------------//
		public function getSaveData()
		{
			$data = [];

			$this->_changedFields = array_unique( $this->_changedFields );
			
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
		public function save(): void
		{
			\Kalibri::model('user')->save( $this->getSaveData() );
		}
		
//------------------------------------------------------------------------------------------------//
		protected function notifyChanged( $field )
		{
			if( is_array( $field ) )
			{
				$this->_changedFields = array_merge( $this->_changedFields, $field );
			}
			else
			{
				$this->_changedFields[] = $field;
			}
		}
	}
}