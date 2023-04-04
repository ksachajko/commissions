<?php

declare(strict_types=1);

namespace App\Dto;

readonly class Transaction
{
    public function __construct(
        public string $bin,
        public string $amount,
        public string $currency,
    ) {
    }
}
