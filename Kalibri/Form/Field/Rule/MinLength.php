<?php

namespace Kalibri\Form\Field\Rule;

class MinLength extends Base {

    protected $length;

    public function __construct($length) {
        $this->length = $length;
    }

    public function validate($value) {
        return is_string($value) && strlen($value) >= $this->length ;
    }

    public function error() {
        return tr('Field must be at least :min-length characters', [
            'min-length'=>$this->length
        ]);
    }
}