<?php

namespace App\DataFixtures\Provider;

use App\Domain\Car\Enum\Type;
use Faker\Generator;
use Faker\Provider\Base;

class CarFixturesProvider extends Base
{
    protected static array $brand = ['Citroën', 'Mercedes'];
    protected static array $type = ['berline', 'citadine', Type::utilitaire->name];
    protected static array $ptra = [3, 7, 19, 32, 44, 100];

    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
    }

    public static function carBrand()
    {
        return static::randomElement(static::$brand);
    }

    public static function carType()
    {
        return static::randomElement(static::$type);
    }

    public static function carPtra()
    {
        return static::randomElement(static::$ptra);
    }
}