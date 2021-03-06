<?php

namespace Labstag\Lib;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ServiceTestLib extends KernelTestCase
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $kernel = self::bootKernel();
        /** @var mixed $doctrine */
        $doctrine            = $kernel->getContainer()->get('doctrine');
        $this->entityManager = $doctrine->getManager();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager);
    }
}
