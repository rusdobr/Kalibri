<?php

namespace Kalibri\Validator {
    /**
     *  @package Kalibri
     *  @subpackage Validation
     *
     *  @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
     */
    class String extends Base
    {
        #[\Override]
        public static function validate( $value, array $rules = null ): void
        {
            trigger_error('String validation not implemented');
        }
    }
}