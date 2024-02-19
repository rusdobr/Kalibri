<?php

namespace Kalibri\Form\Field\Rule;

class Required extends Base {

    #[\Override]
    public function validate($value) {
        return $value !== null;
    }
}