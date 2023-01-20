<?php /** @noinspection PhpUnused */

namespace App\Shared\Application\MessageHandler;

use App\Shared\Domain\Entity\User;
use App\Shared\Domain\Repository\UserRepository;
use App\Shared\Application\Message\Registration;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
class RegistrationHandler
{
    public function __construct(
        public UserPasswordHasherInterface $passwordHasher,
        public UserRepository $repository,
        public LoggerInterface $logger,
    ) {
    }

    public function __invoke(Registration $message): void
    {
        $this->logger->debug(sprintf('Handling registration message for %s', $message->getEmail()));
        $user = new User();
        $user->setEmail($message->getEmail());
        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $message->getPassword()
        );
        $user->setPassword($hashedPassword);
        $this->repository->save($user, true);
        $this->logger->info(sprintf('User registered: %s', $user->getEmail()));
    }
}
