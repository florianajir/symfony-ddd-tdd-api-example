<?php

namespace App\Shared\Infrastructure\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueRelated extends Constraint
{
    public string $message = 'This value is already used.';
    public string $entityClass;
    public string $field;

    public function getRequiredOptions(): array
    {
        return ['entityClass', 'field'];
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
