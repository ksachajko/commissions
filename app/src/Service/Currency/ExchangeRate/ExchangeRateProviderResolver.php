<?php

declare(strict_types=1);

namespace App\Service\Currency\ExchangeRate;

use App\Exception\ExchangeRatesProviderNotFoundException;

class ExchangeRateProviderResolver
{
    private array $providers;

    public function __construct(ExchangeRateProviderInterface ...$providers)
    {
        $this->providers = $providers;
    }

    public function getExchangeRateProvider(string $type): ExchangeRateProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($type)) {
                return $provider;
            }
        }

        throw new ExchangeRatesProviderNotFoundException();
    }
}
