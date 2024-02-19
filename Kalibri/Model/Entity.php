<?php

namespace Kalibri\Model;

use JsonSerializable;
use \Kalibri\Helper\Text;

abstract class Entity implements JsonSerializable
{
	protected $_changedFields = [];
	protected $_modelName;
    protected $_primaryName;
    protected $_forceInsert = false;

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
				[ \Kalibri::app()->getNamespace().'\\App\\Model\\Entity\\', 'Kalibri\\Model\\Entity\\' ],
				'', 
				static::class 
			));
	    }

        if(!$this->_primaryName)
        {
            $this->_primaryName = Text::underscoreToCamel( \Kalibri::model($this->_modelName)->getKeyFieldName() );
        }

	    if( $data !== null )
	    {
			$this->initData( $data );
	    }
	}


    public function getPrimaryValue()
    {
        return $this->{$this->_primaryName};
    }


    public function setPrimaryValue($value)
    {
        $this->{$this->_primaryName} = $value;
        return $this;
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
        $primary = null;
        $model = \Kalibri::model($this->_modelName);

        $primary = $this->_forceInsert
            ? $model->insert($this->getChangedData())
            : $model->save($this->getChangedData());


		if(!$this->getPrimaryValue())
		{
			$this->setPrimaryValue($primary);
		}

        // reset change list
		$this->_changedFields = [];

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

		if($this->getPrimaryValue()) {
			$this->_changedFields[\Kalibri::model($this->_modelName)->getKeyFieldName()] = true;
		}

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
	    if( \method_exists( $this, $name ) && \is_callable( [ $this, $name ] ) )
	    {
		    return \call_user_func( [ &$this, $name ], $arguments );
	    }

	    if( !$this->_withMagic )
	    {
		    \Kalibri::error()->show( 'Method not available: '.static::class.'::'.$name );
	    }

	    $type = null;
	    $fieldName = null;

	    if( str_starts_with($name, 'get') || str_starts_with($name, 'set') )
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
	    	\Kalibri::error()->show( 'Method not available: '.static::class.'::'.$name );
	    }

	    return null;
	}

    public function forceInsert($value)
    {
        $this->_forceInsert = $value;
        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    #[\Override]
    function jsonSerialize()
    {
        return $this->getAllData();
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
