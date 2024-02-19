<?php

namespace Kalibri\Form\Field\Rule;

class RegExp extends Base {

    public function __construct(protected $regexp)
    {
    }

    #[\Override]
    public function validate($value)
    {
        return (bool)preg_match('/'.$this->regexp.'/', (string) $value);
    }

    #[\Override]
    public function error()
    {
        return tr("Field doesn't match required format");
    }
}