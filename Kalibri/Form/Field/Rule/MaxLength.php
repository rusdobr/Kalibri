<?php

namespace Kalibri\Form\Field\Rule;

class MaxLength extends Base {

    public function __construct(protected $length)
    {
    }

    #[\Override]
    public function validate($value) {
        return is_string($value) && strlen($value) <= $this->length ;
    }

    #[\Override]
    public function error() {
        return tr('Field must be no more then :max-length characters', [
            'max-length'=>$this->length
        ]);
    }
}