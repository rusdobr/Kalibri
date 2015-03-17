<?php

namespace Kalibri\Form\Field\Rule;

use Kalibri\Helper\Validate;

class Email extends Base {

    public function validate($value) {
        return Validate::email($value);
    }

    public function error() {
        return tr('Invalid email');
    }
}