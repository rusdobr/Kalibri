<?php

namespace Kalibri\Validator {
    /**
     *  @package Kalibri
     *  @subpackage Validation
     *
     *  @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
     */
    class Base
    {
        protected $_rules = array();
        protected $_value;
        protected $_isValidated = false;
        protected $_isValid = false;

        public function __construct( $value = null, array $rules = null )
        {
            $this->setValue( $value );
            $this->setRules( $rules );
        }

        public function setValue( $value )
        {
            if( $this->_value != $value )
            {
                $this->resetValidation();
                $this->_value = $value;
            }

            return $this;
        }

        public function setRules( array $rules )
        {
            if( $this->_rules != $rules )
            {
                $this->resetValidation();
                $this->_rules = $rules;
            }

            return $this;
        }

        public function addRule( $rules )
        {
            $this->_rules = array_merge( $this->_rules, $rules );
            $this->resetValidation();

            return $this;
        }

        public function resetValidation()
        {
            $this->_isValidated = false;
            return $this;
        }

        public function isValid( $value = null, array $rules = null )
        {
            $this->setValue( $value !== null? $value: $this->_value );
            $this->setRules( $rules !== null? $rules: $this->_rules );

            if( $this->_isValidated )
            {
                return $this->_isValid;
            }

            $this->_isValidated = true;

            return $this->_isValid = static::validate( $this->_value, $this->_rules );
        }

        public static function validate( $value, array $rules = null )
        {
            trigger_error('Validator not implemented');
        }
    }
}