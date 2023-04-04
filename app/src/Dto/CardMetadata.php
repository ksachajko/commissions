<?php

declare(strict_types=1);

namespace App\Dto;

readonly class CardMetadata
{
    public function __construct(public Country $country)
    {
    }
}
