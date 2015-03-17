<?php

namespace Kalibri\Form\Field;

class Email extends Text {

    public function __construct($name = null, $value = null) {
        $this->name('email');
        $this->rule(new Rule\Email());

        parent::__construct($name, $value);
    }
}