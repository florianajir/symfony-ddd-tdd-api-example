<?php

namespace App\Tests\Functional;

use App\Shared\Domain\Entity\User;
use App\Shared\Domain\Repository\UserRepository;

class RegistrationTest extends AuthenticatedTestCase
{
    public function testRegistrationWithWrongEmail(): void
    {
        $this->client->request(
            'POST',
            '/api/registration',
            content: $this->getUserJsonFixture('stinky')
        );
        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('email', $data);

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findByEmail(self::TEST_EMAIL);
        self::assertNull($testUser);
    }

    public function testRegistrationWithoutPassword(): void
    {
        $this->client->request(
            'POST',
            '/api/registration',
            content: $this->getUserJsonFixture(password: '')
        );
        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('password', $data);

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findByEmail(self::TEST_EMAIL);
        self::assertNull($testUser);
    }

    public function testRegistration(): void
    {
        $this->client->request(
            'POST',
            '/api/registration',
            content: $this->getUserJsonFixture()
        );
        $this->assertResponseIsSuccessful();

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findByEmail(self::TEST_EMAIL);
        self::assertInstanceOf(User::class, $testUser);
    }

    public function testLogin()
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
}
