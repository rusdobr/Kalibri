<?php

namespace Kalibri\Validator {
    /**
     *  @package Kalibri
     *  @subpackage Validation
     *
     *  @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
     */
    class Email extends Base
    {
        public static function validate( $value, array $rules = null )
        {
            return (bool)filter_var( $value, FILTER_VALIDATE_EMAIL );
        }
    }
}