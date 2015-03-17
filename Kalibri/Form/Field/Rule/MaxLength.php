<?php

namespace Kalibri\Form\Field\Rule;

class MaxLength extends Base {

    protected $length;

    public function __construct($length) {
        $this->length = $length;
    }

    public function validate($value) {
        return is_string($value) && strlen($value) <= $this->length ;
    }

    public function error() {
        return tr('Field must be no more then :max-length characters', [
            'max-length'=>$this->length
        ]);
    }
}