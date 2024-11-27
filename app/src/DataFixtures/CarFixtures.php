<?php

namespace App\DataFixtures;

use App\DataFixtures\Factory\CarFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CarFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        CarFactory::createMany(100);
        $manager->flush();
    }
}
