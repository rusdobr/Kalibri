<?php

namespace Kalibri\Form\Field\Rule;

class RegExp extends Base {

    protected $regexp;

    public function __construct($regexp)
    {
        $this->regexp = $regexp;
    }

    public function validate($value)
    {
        return (bool)preg_match($this->regexp, $value);
    }

    public function error()
    {
        return tr("Field doesn't match required format");
    }
}