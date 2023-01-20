<?php

namespace App\Tests\Functional;

use App\Shared\Domain\Entity\User;
use App\Shared\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationTest extends WebTestCase
{
    private const TEST_EMAIL = 'test@pm.me';
    private const TEST_PASSWORD = 'foobar';
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
    }

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
        // TODO test jwt validity on restricted endpoint
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

    private function getUserJsonFixture($email = self::TEST_EMAIL, $password = self::TEST_PASSWORD): string
    {
        return json_encode(compact('email', 'password'));
    }

    private function createUserFixture($email = self::TEST_EMAIL, $password = self::TEST_PASSWORD)
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
