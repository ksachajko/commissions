<?php

declare(strict_types=1);

namespace App\Tests\Service\Card\Metadata;

use App\Exception\CardMetadataProviderNotFoundException;
use App\Service\Card\Metadata\MetadataProviderInterface;
use App\Service\Card\Metadata\MetadataProviderResolver;
use App\Service\Card\Metadata\Providers\BinlistProvider;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class MetadataProviderResolverTest extends TestCase
{
    public function testThrowExceptionWhenNoProvidersAvailable(): void
    {
        $this->expectException(CardMetadataProviderNotFoundException::class);

        $metadataProviderResolver = new MetadataProviderResolver();

        $metadataProviderResolver->getMetadataProvider('notRegistered');
    }

    public function testThrowExceptionWhenNoMatchingProviderFound(): void
    {
        $this->expectException(CardMetadataProviderNotFoundException::class);

        $metadataProvider = new BinlistProvider($this->createMock(Client::class));
        $metadataProviderResolver = new MetadataProviderResolver($metadataProvider);

        $metadataProviderResolver->getMetadataProvider('notRegistered');
    }

    public function testReturnMatchedProvider(): void
    {
        $metadataProvider = $this->createMock(MetadataProviderInterface::class);
        $metadataProvider->method('supports')->with('binlist')->willReturn(false);

        $metadataProvider2 = new BinlistProvider($this->createMock(Client::class));
        $metadataProviderResolver = new MetadataProviderResolver($metadataProvider, $metadataProvider2);

        $this->assertSame($metadataProvider2, $metadataProviderResolver->getMetadataProvider('binlist'));
    }
}
