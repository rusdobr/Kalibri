<?php

namespace Kalibri\Form\Field\Rule;

class Required extends Base {

    public function validate($value) {
        return $value !== null;
    }
}