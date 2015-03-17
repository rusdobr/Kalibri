<?php

namespace Kalibri\Form\Field\Rule;

abstract class Base {
    abstract public function validate($value);
    abstract public function error();
}