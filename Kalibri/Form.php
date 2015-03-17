<?php

namespace Kalibri;

use Kalibri\Form\Field\Base;
use Kalibri\Form\Field\Rule\Required;

class Form {

    protected $_fields = [];
    protected $_isValid;

    public function add(Base $field, $isRequired = true)
    {
        if($isRequired)
        {
            //$field->rule(new Required());
        }

        $this->_fields[$field->name()] = $field;

        return $this;
    }

    public function required(Base $field)
    {
        return $this->add($field, true);
    }

    public function optional(Base $field)
    {
        return $this->add($field, false);
    }

    public function fillWith( array $data )
    {

        foreach( $this->_fields as $field )
        {
            if( isset( $data[$field->name()] ))
            {
                $field->value( $data[$field->name()] );
            }
        }

        return $this;
    }

    public function errors()
    {
        $result = [];

        foreach($this->_fields as $field)
        {
            if( !$field->isValid() )
            {
                $result = array_merge( $result, $field->errors() );
            }
        }

        return $result;
    }

    public function validate()
    {
        $isValid = true;

        foreach($this->_fields as $field)
        {
            /** @var \Kalibri\Form\Field\Base $field */
            $isValid = $isValid && $field->isValid();
        }

        return $isValid;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if($this->_isValid === null)
        {
            $this->_isValid = $this->validate();
        }

        return $this->_isValid;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function value($name)
    {
        return isset($this->_fields[$name])? $this->_fields[$name]->value(): null;
    }

    /**
     * @param string $name
     *
     * @return \Kalibri\Form\Base
     */
    public function field($name)
    {
        return isset($this->_fields[$name])? $this->_fields[$name]: null;
    }
}