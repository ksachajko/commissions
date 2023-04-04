<?php

declare(strict_types=1);

namespace App\Service\Card\Metadata;

use App\Dto\CardMetadata;

interface MetadataProviderInterface
{
    public function supports(string $type): bool;

    public function get(string $bin): CardMetadata;
}
