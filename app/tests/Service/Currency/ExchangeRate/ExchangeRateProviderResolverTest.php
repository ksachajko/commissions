<?php

declare(strict_types=1);

namespace App\Tests\Service\Currency\ExchangeRate;

use App\Exception\ExchangeRatesProviderNotFoundException;
use App\Service\Currency\ExchangeRate\ExchangeRateProviderInterface;
use App\Service\Currency\ExchangeRate\ExchangeRateProviderResolver;
use App\Service\Currency\ExchangeRate\Providers\ExchangeRateApiIo;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ExchangeRateProviderResolverTest extends TestCase
{
    public function testThrowExceptionWhenNoProvidersAvailable(): void
    {
        $this->expectException(ExchangeRatesProviderNotFoundException::class);

        $exchangeRateProviderResolver = new ExchangeRateProviderResolver();

        $exchangeRateProviderResolver->getExchangeRateProvider('notRegistered');
    }

    public function testThrowExceptionWhenNoMatchingProviderFound(): void
    {
        $this->expectException(ExchangeRatesProviderNotFoundException::class);

        $exchangeRateApiIo = new ExchangeRateApiIo($this->createMock(Client::class));
        $exchangeRateProviderResolver = new ExchangeRateProviderResolver($exchangeRateApiIo);

        $exchangeRateProviderResolver->getExchangeRateProvider('notRegistered');
    }

    public function testReturnMatchedProvider(): void
    {
        $exchangeRateProvider1 = $this->createMock(ExchangeRateProviderInterface::class);
        $exchangeRateProvider1->method('supports')->with('exchangerateapiio')->willReturn(false);

        $exchangeRateProvider2 = new ExchangeRateApiIo($this->createMock(Client::class));
        $exchangeRateProviderResolver = new ExchangeRateProviderResolver($exchangeRateProvider1, $exchangeRateProvider2);

        $this->assertSame($exchangeRateProvider2, $exchangeRateProviderResolver->getExchangeRateProvider('exchangerateapiio'));
    }
}
