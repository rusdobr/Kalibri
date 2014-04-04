<?php

namespace Kalibri\Model {

    use \Kalibri\Helper\Text;

    abstract class Entity
    {
	protected $_changedFields = array();
	protected $_modelName;

	/**
	 *  Enable or disable magic getters and setters
	 *  @var bool
	 */
	protected $_withMagic = true;

	public function __construct( array $data = null )
	{
	    if( !$this->_modelName )
	    {
		$this->_modelName = strtolower(
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
	 *  Mark field as changed
	 *
	 *  @param $field string
	 *
	 *  @return \Kalibri\Model\Entity
	 */
	public function registerChanged( $field )
	{
	    if( is_array( $field ) )
	    {
		$this->_changedFields = array_merge( $this->_changedFields, array_flip( $field ) );
	    }
	    else
	    {
		$this->_changedFields[ $field ] = true;
	    }

	    return $this;
	}

	/**
	 *  Store all changed fields of current entity
	 *
	 *  @return \Kalibri\Model\Entity
	 */
	public function save()
	{
	    \Kalibri::model( $this->_modelName )->save( $this->getChangedData() );
	    // reset change list
	    $this->_changedFields = array();

	    return $this;
	}

	/**
	 *  Get all changed fields as array. Format is $field=>$value
	 *	
	 *  @return array
	 */
	public function getChangedData()
	{
	    $data = $this->getAllData();

	    foreach( $data as $field=>$v )
	    {
		if( !isset( $this->_changedFields[ $field ] ) )
		{
		    unset( $data[ $field ] );
		}
	    }

	    return $data;
	}

	/**
	 *  Called on each method request. Will try to find appropriete action for field.
	 *
	 *  @param string $name Method name
	 *  @param string $arguments Arguments passed to method
	 *
	 *  @return mixed
	 */
	public function __call( $name,  $arguments )
	{
	    if( \method_exists( $this, $name ) && \is_callable( array( $this, $name ) ) )
	    {
		return \call_user_func( array( &$this, $name ), $arguments );
	    }

	    if( !$this->_withMagic )
	    {
		\Kalibri::error()->show( 'Method not available: '.get_class($this).'::'.$name );
	    }

	    $type = null;
	    $fieldName = null;

	    if( strpos( $name, 'get') === 0 || strpos($name, 'set') === 0 )
	    {
		$type = substr( $name, 0, 3 );
		$fieldName = substr( $name, 3 );
		$fieldName = strtolower( $fieldName[0] ).substr( $fieldName, 1 );
	    }

	    if( property_exists( $this, $fieldName ) )
	    {
		switch( $type )
		{
		    case 'get':
			return $this->$fieldName;
		    case 'set':
			$this->$fieldName = current( $arguments );
			$this->registerChanged( Text::camelToUnderscore( $fieldName ) );
			return $this;
		}
	    }
	    else
	    {
		\Kalibri::error()->show( 'Method not available: '.get_class($this).'::'.$name );
	    }

	    return null;
	}

	/**
	 *  Data initialization
	 *
	 *  @param $data array Row data from db
	 */
	abstract public function initData( array $data );

	/**
	 *  Get all data as array. Format is $field=>$value
	 *
	 *  @return array
	 */
	abstract public function getAllData();
    }
}