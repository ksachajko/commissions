<?php

declare(strict_types=1);

namespace App\Tests\Service\Card\Metadata\Providers;

use App\Dto\CardMetadata;
use App\Dto\Country;
use App\Exception\CardMetadataFetchFailedException;
use App\Service\Card\Metadata\Providers\BinlistProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class BinlistProviderTest extends TestCase
{
    private Client $httpClient;
    private BinlistProvider $binlistProvider;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(Client::class);
        $this->binlistProvider = new BinlistProvider($this->httpClient);
    }

    #[TestWith(['', false])]
    #[TestWith(['notSupported', false])]
    #[TestWith(['binlist', true])]
    public function testSupports(string $type, bool $expected): void
    {
        $this->assertSame($expected, $this->binlistProvider->supports($type));
    }

    public function testThrowsExceptionWhenHttpError(): void
    {
        $this->expectException(CardMetadataFetchFailedException::class);

        $this->httpClient
            ->method('get')
            ->with('/123456')
            ->willThrowException($this->createMock(GuzzleException::class));

        $this->binlistProvider->get('123456');
    }

    public function testReturnsCardMetadata(): void
    {
        $response = $this->createSuccessfulResponse();
        $this->httpClient
            ->method('get')
            ->with('/123456')
            ->willReturn($response);

        $expected = new CardMetadata(new Country('DK'));

        $this->assertEquals($expected, $this->binlistProvider->get('123456'));
    }

    public function testReturnsSameCardMetadataFromCache(): void
    {
        $response = $this->createSuccessfulResponse();
        $this->httpClient
            ->expects($this->exactly(1))
            ->method('get')
            ->with('/123456')
            ->willReturn($response);

        $expected = new CardMetadata(new Country('DK'));

        $this->binlistProvider->get('123456');
        $this->binlistProvider->get('123456');
        $this->assertEquals($expected, $this->binlistProvider->get('123456'));
    }

    private function createSuccessfulResponse(): Response
    {
        $responseData = [
            'scheme' => 'visa',
            'country' => [
                'name' => 'Denmark',
                'alpha2' => 'DK',
            ],
        ];

        return new Response(200, [], json_encode($responseData));
    }
}
