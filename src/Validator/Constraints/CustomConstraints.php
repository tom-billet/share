<?php
// src/Validator/Constraints/CustomConstraints.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CustomConstraints extends Constraint
{
    public $message = 'The value "{{ value }}" is not valid.';
}