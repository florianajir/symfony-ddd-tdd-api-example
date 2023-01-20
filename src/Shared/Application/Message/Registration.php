<?php

namespace App\Shared\Application\Message;

use App\Shared\Domain\Entity\User;
use App\Shared\Infrastructure\Validator\Constraint as DomainAssert;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class Registration
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[OA\Property(type: 'string', maxLength: 180)]
    #[DomainAssert\UniqueRelated(['field' => 'email', 'entityClass' => User::class])]
    private string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    #[OA\Property(type: 'string', maxLength: 255, minLength: 6)]
    private string $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
