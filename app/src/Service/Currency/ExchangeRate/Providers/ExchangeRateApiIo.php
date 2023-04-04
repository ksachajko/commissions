<?php

declare(strict_types=1);

namespace App\Service\Currency\ExchangeRate\Providers;

use App\Exception\ExchangeRatesFetchFailedException;
use App\Service\Currency\ExchangeRate\ExchangeRateProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ExchangeRateApiIo implements ExchangeRateProviderInterface
{
    private array $cache = [];
    private Client $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function supports(string $type): bool
    {
        return $type == 'exchangerateapiio';
    }

    public function getRate(string $currency): float
    {
        if (empty($this->cache)) {
            $this->fetchRates();
        }

        return $this->cache[$currency];
    }

    private function fetchRates(): void
    {
        try {
            $response = $this->httpClient->get('/exchangerates_data/latest');
        } catch (GuzzleException $e) {
            throw new ExchangeRatesFetchFailedException($e->getMessage());
        }

        $responseContent = json_decode($response->getBody()->getContents());

        foreach ($responseContent->rates as $currency => $rate) {
            $this->cache[$currency] = $rate;
        }
    }
}
