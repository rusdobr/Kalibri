<?php

namespace Kalibri\Form\Field\Rule;

use Kalibri\Helper\Validate;

class Email extends Base {

    #[\Override]
    public function validate($value) {
        return Validate::email($value);
    }

    #[\Override]
    public function error() {
        return tr('Invalid email');
    }
}