<?php

declare(strict_types=1);

namespace App;

use App\Service\Card\Metadata\MetadataProviderResolver;
use App\Service\Card\Metadata\Providers\BinlistProvider;
use App\Service\Commission\CommissionRateProvider;
use App\Service\Commission\CommissionService;
use App\Service\Currency\ExchangeRate\ExchangeRateProviderResolver;
use App\Service\Currency\ExchangeRate\Providers\ExchangeRateApiIo;
use App\Service\Transaction\TransactionRepository;
use GuzzleHttp\Client;

require __DIR__.'/../vendor/autoload.php';

// TODO add dependency injection lib
$binlistHttpClient = new Client([
    'base_uri' => 'https://lookup.binlist.net',
]);
$cardMetadataProviderResolver = new MetadataProviderResolver(new BinlistProvider($binlistHttpClient));

$exchangeRatesHttpClient = new Client([
    'base_uri' => 'https://api.apilayer.com',
    'headers' => [
        'apikey' => 'K6aOIecU6CqGK57ruXl9FgZzOxNbAr0B',
    ],
]);
$exchangeRateApiIoProvider = new ExchangeRateApiIo($exchangeRatesHttpClient);
$exchangeRateProviderResolver = new ExchangeRateProviderResolver($exchangeRateApiIoProvider);

$cardMetadataProvider = $cardMetadataProviderResolver->getMetadataProvider($argv[1] ?? 'binlist');
$exchangeRateProvider = $exchangeRateProviderResolver->getExchangeRateProvider($argv[2] ?? 'exchangerateapiio');

$commissionRateProvider = new CommissionRateProvider($cardMetadataProvider);
$service = new CommissionService($commissionRateProvider, $exchangeRateProvider);

$repository = new TransactionRepository();
$transactionsList = $repository->getAll();

foreach ($transactionsList->asArray() as $transaction) {
    echo $service->calculateCommissionFee($transaction).PHP_EOL;
}
