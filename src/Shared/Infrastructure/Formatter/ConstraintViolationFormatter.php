<?php

namespace App\Shared\Infrastructure\Formatter;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationFormatter
{
    public static function formatToArray(ConstraintViolationListInterface $errors): array
    {
        $formatted = array();
        foreach ($errors as $error) {
            $formatted[$error->getPropertyPath()] = $error->getMessage();
        }

        return $formatted;
    }
}
