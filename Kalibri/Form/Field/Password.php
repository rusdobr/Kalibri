<?php

namespace Kalibri\Form\Field;

class Password extends Text {

    public function __construct($name = null, $value = null) {
        $this->name('password');
        parent::__construct($name, $value);
    }
}