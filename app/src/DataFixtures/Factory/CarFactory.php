<?php

namespace App\DataFixtures\Factory;

use App\DataFixtures\Provider\CarFixturesProvider;
use App\Domain\Car\Enum\Type;
use App\Entity\Car;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Car>
 */
final class CarFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Car::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'brand' => CarFixturesProvider::carBrand(),
            'type' => CarFixturesProvider::carType(),
            'seats' => self::faker()->numberBetween(1, 10),
            'color' => self::faker()->colorName(),
        ];
    }

    protected function initialize(): static
    {
        return $this
             ->afterInstantiate(function(Car $car): void {
                 if ($car->getType() === Type::utilitaire->name) {

                     $car->setPtra(CarFixturesProvider::carPtra());
                 }
             })
        ;
    }
}
