<?php

declare(strict_types=1);

namespace App\Dto;

readonly class Country
{
    public function __construct(public string $alpha2)
    {
    }
}
