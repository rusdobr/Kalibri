<?php

namespace Kalibri\Form\Field;

class Base
{
    protected $_value;
    protected $_name;
    protected $_isValid;
    protected $_errors = [];
    protected $_rules = [];

    public function __construct($name = null, $value = null) {
        $this->name($name);
        $this->value($value);
    }

    public function name($value = null) {
        if($value !== null) {
            $this->_name = $value;
        }

        return $this->_name;
    }

    public function value($value = null) {

        if($value !== null) {
            $this->_value = $value;
        }

        return $this->_value;
    }

    public function validate() {
        $result = true;

        foreach($this->_rules as $rule) {

            if( !$rule->validate( $this->value() ) ) {
                $this->_errors[] = $rule->error();
                $result = false;
            }
        }

        return $result;
    }

    public function isValid() {
        if($this->_isValid === null) {
            $this->_isValid = $this->validate();
        }

        return $this->_isValid;
    }

    public function errors() {
        return $this->_errors;
    }

    public function rule(Rule\Base $rule) {
        $this->_rules[] = $rule;
        return $this;
    }
}