<?php

declare(strict_types=1);

namespace App\Service\Currency\ExchangeRate;

interface ExchangeRateProviderInterface
{
    public function supports(string $type): bool;

    public function getRate(string $currency): float;
}
