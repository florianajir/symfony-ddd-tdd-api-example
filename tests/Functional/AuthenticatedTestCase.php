<?php

namespace App\Tests\Functional;

use App\Shared\Domain\Entity\User;
use App\Shared\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AuthenticatedTestCase extends WebTestCase
{
    protected const TEST_EMAIL = 'test@pm.me';
    protected const TEST_PASSWORD = 'foobar';
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findByEmail(self::TEST_EMAIL);
        if (null !== $testUser) {
            $userRepository->remove($testUser, true);
        }
    }

    protected function authenticateWithJwt()
    {
        $this->createUserFixture();
        $this->client->request(
            'POST',
            '/api/login',
            content: $this->getUserJsonFixture()
        );
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $data);
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
    }

    protected function getUserJsonFixture($email = self::TEST_EMAIL, $password = self::TEST_PASSWORD): string
    {
        return json_encode(compact('email', 'password'));
    }

    protected function createUserFixture($email = self::TEST_EMAIL, $password = self::TEST_PASSWORD)
    {
        $user = new User();
        $user->setEmail($email);
        $hashedPassword = static::getContainer()->get(UserPasswordHasherInterface::class)
            ->hashPassword(
                $user,
                $password
            );
        $user->setPassword($hashedPassword);
        static::getContainer()->get(UserRepository::class)->save($user, true);
    }
}
