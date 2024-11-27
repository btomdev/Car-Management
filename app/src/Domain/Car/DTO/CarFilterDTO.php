<?php

namespace App\Domain\Car\DTO;
class CarFilterDTO
{
    public ?string $brand = null;
    public ?string $type = null;
    public ?int $seats = null;
    public ?string $color = null;
    public ?float $ptra = null;
    public ?string $sort = null;
}