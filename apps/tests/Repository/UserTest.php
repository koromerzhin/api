<?php

namespace Labstag\Tests\Repository;

use Labstag\Entity\User;
use Labstag\Lib\RepositoryTestLib;
use Labstag\Repository\UserRepository;

/**
 * @internal
 * @coversNothing
 */
class UserTest extends RepositoryTestLib
{

    /**
     * @var UserRepository
     */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        /** @var UserRepository $repository */
        $repository       = $this->entityManager->getRepository(
            User::class
        );
        $this->repository = $repository;
    }

    public function testFindAll(): void
    {
        $all = $this->repository->findAll();
        $this->assertTrue(is_array($all));
    }

    public function testfindOneRandom(): void
    {
        $all = $this->repository->findAll();
        if (0 != count($all)) {
            $random = $this->repository->findOneRandom();
            $this->assertSame(get_class($random), User::class);

            return;
        }

        $this->assertTrue(true);
    }

    public function testloginToken(): void
    {
        /** @var null $empty */
        $empty = $this->repository->loginToken(null);
        $this->AssertNull($empty);
        /** @var ?User $user */
        $user = $this->repository->findOneRandom(
            'e.apiKey IS NOT NULL AND e.apiKey!=:apikey',
            ['apikey' => '']
        );
        if ($user instanceof User) {
            /** @var User $user */
            $user = $this->repository->loginToken($user->getApiKey());
            $this->assertSame(get_class($user), User::class);

            return;
        }

        $this->assertTrue(true);
    }

    public function testlogin(): void
    {
        /** @var null $empty */
        $empty = $this->repository->login(null);
        $this->AssertNull($empty);
        /** @var ?User $user */
        $user = $this->repository->findOneRandom();
        if ($user instanceof User) {
            /** @var User $login */
            $login = $this->repository->login(
                $user->getUsername()
            );
            $this->assertSame(get_class($login), User::class);
            /** @var User $email */
            $email = $this->repository->login(
                $user->getEmail()
            );
            $this->assertSame(get_class($email), User::class);

            return;
        }

        $this->assertTrue(true);
    }
}
