<?php

declare(strict_types=1);

namespace App\Tests\Service\Currency\ExchangeRate\Providers;

use App\Exception\ExchangeRatesFetchFailedException;
use App\Service\Currency\ExchangeRate\Providers\ExchangeRateApiIo;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class ExchangeRateApiIoTest extends TestCase
{
    private Client $httpClient;
    private ExchangeRateApiIo $exchangeRateApiIo;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(Client::class);
        $this->exchangeRateApiIo = new ExchangeRateApiIo($this->httpClient);
    }

    #[TestWith(['', false])]
    #[TestWith(['notSupported', false])]
    #[TestWith(['exchangerateapiio', true])]
    public function testSupports(string $type, bool $expected): void
    {
        $this->assertSame($expected, $this->exchangeRateApiIo->supports($type));
    }

    public function testThrowsExceptionWhenHttpError(): void
    {
        $this->expectException(ExchangeRatesFetchFailedException::class);

        $this->httpClient
            ->method('get')
            ->with('/exchangerates_data/latest')
            ->willThrowException($this->createMock(GuzzleException::class));

        $this->exchangeRateApiIo->getRate('USD');
    }

    public function testReturnsCardMetadata(): void
    {
        $response = $this->createSuccessfulResponse();
        $this->httpClient
            ->method('get')
            ->with('/exchangerates_data/latest')
            ->willReturn($response);

        $this->assertEquals(1.086926, $this->exchangeRateApiIo->getRate('USD'));
    }

    public function testReturnsSameCardMetadataFromCache(): void
    {
        $response = $this->createSuccessfulResponse();
        $this->httpClient
            ->expects($this->exactly(1))
            ->method('get')
            ->with('/exchangerates_data/latest')
            ->willReturn($response);

        $this->exchangeRateApiIo->getRate('USD');
        $this->exchangeRateApiIo->getRate('USD');
        $this->assertEquals(1.086926, $this->exchangeRateApiIo->getRate('USD'));
    }

    private function createSuccessfulResponse(): Response
    {
        $responseData = [
            'rates' => [
                'USD' => 1.086926,
            ],
        ];

        return new Response(200, [], json_encode($responseData));
    }
}
