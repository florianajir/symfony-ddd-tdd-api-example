<?php

namespace App\Shared\Infrastructure\Validator\Constraint;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueRelatedValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueRelated) {
            throw new UnexpectedTypeException($constraint, UniqueRelated::class);
        }
        $entityRepository = $this->entityManager->getRepository($constraint->entityClass);
        $searchResults = $entityRepository->findBy([
            $constraint->field => $value,
        ]);
        if (count($searchResults) > 0) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
