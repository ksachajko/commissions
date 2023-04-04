<?php

declare(strict_types=1);

namespace App\Service\Card\Metadata;

use App\Exception\CardMetadataProviderNotFoundException;

class MetadataProviderResolver
{
    private array $providers;

    public function __construct(MetadataProviderInterface ...$providers)
    {
        $this->providers = $providers;
    }

    public function getMetadataProvider(string $type): MetadataProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($type)) {
                return $provider;
            }
        }

        throw new CardMetadataProviderNotFoundException();
    }
}
