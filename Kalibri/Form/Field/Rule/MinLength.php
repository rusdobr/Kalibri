<?php

namespace Kalibri\Form\Field\Rule;

class MinLength extends Base {

    public function __construct(protected $length)
    {
    }

    #[\Override]
    public function validate($value) {
        return is_string($value) && strlen($value) >= $this->length ;
    }

    #[\Override]
    public function error() {
        return tr('Field must be at least :min-length characters', [
            'min-length'=>$this->length
        ]);
    }
}