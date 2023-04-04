<?php

declare(strict_types=1);

namespace App\Service\Commission;

use App\Dto\Transaction;
use App\Service\Card\Metadata\MetadataProviderInterface;

class CommissionRateProvider
{
    public const EUROPEAN_COUNTRIES_CODES = [
        'AT', 'BE', 'BG', 'HR', 'CY',
        'CZ', 'DK', 'EE', 'FI', 'ES',
        'FR', 'DE', 'GR', 'HU', 'IE',
        'IT', 'LV', 'LT', 'LU', 'MT',
        'NL', 'PL', 'PT', 'RO', 'SK',
        'SI', 'ES', 'SE',
    ];
    public const RATE_EU = 0.01;
    public const RATE_NON_EU = 0.02;

    private MetadataProviderInterface $cardMetadataProvider;

    public function __construct(MetadataProviderInterface $cardMetadataProvider)
    {
        $this->cardMetadataProvider = $cardMetadataProvider;
    }

    public function getRate(Transaction $transaction): float
    {
        $cardMetadata = $this->cardMetadataProvider->get($transaction->bin);

        return in_array($cardMetadata->country->alpha2, self::EUROPEAN_COUNTRIES_CODES) ? self::RATE_EU : self::RATE_NON_EU;
    }
}
