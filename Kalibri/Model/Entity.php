<?php

namespace Kalibri\Model
{
	abstract class Entity
	{
		protected $changed = array();
		protected $modelName;
		
		public function __construct( array $data = null )
		{
			if( !$this->modelName )
			{
				$this->modelName = strtolower(
					str_replace( 
						array( \Kalibri::app()->getNamespace().'\\App\\Model\\Entity\\', 'Kalibri\\Model\\Entity\\' ), 
						'', 
						get_class( $this ) 
				));
			}
			
			if( $data !== null )
			{
				$this->initData( $data );
			}
		}
		
		/**
		 *	Mark field as changed
		 *
		 *	@param $field string
		 *
		 *	@return \Kalibri\Model\Entity
		 */
		public function registerChanged( $field )
		{
			if( is_array( $field ) )
			{
				$this->changed = array_merge( $this->changed, array_flip( $field ) );
			}
			else
			{
				$this->changed[ $field ] = true;
			}
			
			return $this;
		}
		
		/**
		 *	Store all changed fields of current entity
		 *
		 *	@return \Kalibri\Model\Entity
		 */
		public function save()
		{
			\Kalibri::model( $this->modelName )->save( $this->getChangedData() );
			// reset change list
			$this->changed = array();
			
			return $this;
		}
		
		/**
		 *	Get all changed fields as array. Format is $field=>$value
		 *	
		 *	@return array
		 */
		public function getChangedData()
		{
			$data = $this->getAllData();
			
			foreach( $data as $field=>$v )
			{
				if( !isset( $this->changed[ $field ] ) )
				{
					unset( $data[ $field ] );
				}
			}
			
			return $data;
		}
		
		/**
		 *	Data initialization
		 *
		 *	@param $data array Row data from db
		 */
		abstract public function initData( array $data );
		
		/**
		 *	Get all data as array. Format is $field=>$value
		 *
		 *	@return array
		 */
		abstract public function getAllData();
	}
}