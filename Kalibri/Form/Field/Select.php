<?php

namespace Kalibri\Form\Field;

use Kalibri\Form\Field\Base;

class Select extends Base {

    protected $_enum = [];

    public function enum(array $value = null) {
        if($value != null) {
            $this->_enum = $value;
        }

        return $this->_enum;
    }
}