<?php

namespace Kalibri\Validator
{
    /**
     *  @package Kalibri
     *  @subpackage Validation
     *
     *  @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
     */
    class Number extends Base
    {
        const RULE_EQUAL = '=';
        const RULE_LESS = '<';
        const RULE_LESS_EQUAL = '<=';
        const RULE_GREATER = '>';
        const RULE_GREATER_EQUAL = '>=';
        const RULE_INTEGER = 'int';
        const RULE_FLOAT = 'float';
        const RULE_BINARY = 'bin';
        const RULE_OCTAL = 'octal';
        const RULE_HEX = 'hex';
        const RULE_UNSIGNED = 'unsigned';

        public static function validate( $value, array $rules = null )
        {
            $isValid = is_numeric( $value );

            if( is_array( $rules ) && $isValid )
            {
                $rules = self::normalizeRules( $rules );

                foreach( $rules as $rule=>$compareTo )
                {
                    switch( $rule )
                    {
                        case self::RULE_EQUAL:
                            $isValid = $value == $compareTo;
                            break;
                        case self::RULE_LESS:
                            $isValid = $value < $compareTo;
                            break;
                        case self::RULE_GREATER:
                            $isValid = $value > $compareTo;
                            break;
                        case self::RULE_GREATER_EQUAL:
                            $isValid = $value >= $compareTo;
                            break;
                        case self::RULE_LESS_EQUAL:
                            $isValid = $value <= $compareTo;
                            break;
                        case self::RULE_INTEGER:
                            $isValid = filter_var( $value, FILTER_VALIDATE_INT );
                            break;
                        case self::RULE_FLOAT:
                            $isValid = filter_var( $value, FILTER_VALIDATE_FLOAT );
                            break;
                        case self::RULE_UNSIGNED:
                            $isValid = $value >= 0;
                            break;
                        case self::RULE_BINARY:
                            trigger_error('Validating binary not implemented');
                            break;
                        case self::RULE_OCTAL:
                            trigger_error('Validating octal not implemented');
                            break;
                        case self::RULE_HEX:
                            trigger_error('Validating hex not implemented');
                            break;
                    }

                    // Prevent further validation of invalid value
                    if( !$isValid )
                    {
                        break;
                    }
                }
            }

            return $isValid;
        }

        protected static function normalizeRules( array $rules )
        {
            $withParam = array(
                self::RULE_LESS_EQUAL,
                self::RULE_LESS,
                self::RULE_GREATER_EQUAL,
                self::RULE_GREATER,
                self::RULE_EQUAL
            );

            $withParamCount = count( $withParam );
            $normalized = array();
            $added = false;

            foreach( $rules as $rule )
            {
                $added = false;
                for( $i=0; $i < $withParamCount; $i++ )
                {
                    if( strpos( $rule, $withParam[ $i ] ) === 0 )
                    {
                        $normalized[ $withParam[$i] ] = str_replace( $withParam[$i], '', $rule );
                        // Convert string to numeric
                        $normalized[ $withParam[$i] ] = strpos( $normalized[ $withParam[$i] ], '.' ) !== false
                            ? floatval( $normalized[ $withParam[$i] ] )
                            : intval( $normalized[ $withParam[$i] ] );
                        $added = true;
                        break;
                    }
                }

                if( !$added )
                {
                    $normalized[ $rule ] = true;
                }
            }

            return $normalized;
        }
    }
}