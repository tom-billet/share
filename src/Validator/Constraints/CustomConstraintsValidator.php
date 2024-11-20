<?php
// src/Validator/Constraints/CustomConstraintsValidator.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CustomConstraintsValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!is_string($value)) {
            return;
        }

        // Validate length
        if (strlen($value) > 255) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }

        // Validate allowed characters
        if (!preg_match('/^[a-zA-Z0-9\s]+$/', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}