<?php

namespace Kalibri\Validator {
    /**
     *  @package Kalibri
     *  @subpackage Validation
     *
     *  @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
     */
    class Boolean extends Base
    {
        public const RULE_TRUE = 'true';
        public const RULE_FALSE = 'false';
        public const RULE_EXACT_TRUE_OR_FALSE = 'true or false';
        public const RULE_EXACT_1_OR_0 = '1 or 0';

        #[\Override]
        public static function validate( $value, array $rules = null )
        {
            $isValid = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) !== null;

            if( is_array( $rules ) && $isValid )
            {
                foreach( $rules as $rule )
                {
                    switch( $rule )
                    {
                        case self::RULE_TRUE:
                            $isValid = (bool)$value == true;
                            break;
                        case self::RULE_FALSE:
                            $isValid = (bool)$value == false;
                            break;
                        case self::RULE_EXACT_1_OR_0:
                            $isValid = $value == 0 || $value == 1;
                            break;
                        case self::RULE_EXACT_TRUE_OR_FALSE:
                            $isValid = $value == 'true' || $value == 'true';
                            break;
                    }

                    if( !$isValid )
                    {
                        break;
                    }
                }
            }

            return $isValid;
        }
    }
}