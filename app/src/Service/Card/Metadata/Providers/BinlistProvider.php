<?php

declare(strict_types=1);

namespace App\Service\Card\Metadata\Providers;

use App\Dto\CardMetadata;
use App\Dto\Country;
use App\Exception\CardMetadataFetchFailedException;
use App\Service\Card\Metadata\MetadataProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BinlistProvider implements MetadataProviderInterface
{
    private array $cache = [];
    private Client $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function supports(string $type): bool
    {
        return 'binlist' == $type;
    }

    public function get(string $bin): CardMetadata
    {
        if (array_key_exists($bin, $this->cache)) {
            return $this->cache[$bin];
        }

        try {
            $response = $this->httpClient->get('/'.$bin);
        } catch (GuzzleException $e) {
            throw new CardMetadataFetchFailedException($e->getMessage());
        }

        $responseContent = json_decode($response->getBody()->getContents());

        $country = new Country($responseContent->country->alpha2);
        $cardMetadata = new CardMetadata($country);

        $this->cache[$bin] = $cardMetadata;

        return $cardMetadata;
    }
}
