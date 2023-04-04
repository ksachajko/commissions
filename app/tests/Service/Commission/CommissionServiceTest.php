<?php

declare(strict_types=1);

namespace App\Tests\Service\Commission;

use App\Dto\Transaction;
use App\Service\Commission\CommissionRateProvider;
use App\Service\Commission\CommissionService;
use App\Service\Currency\ExchangeRate\ExchangeRateProviderInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CommissionServiceTest extends TestCase
{
    private CommissionRateProvider $commissionRateProvider;
    private ExchangeRateProviderInterface $exchangeRateProvider;
    private CommissionService $commissionService;

    protected function setUp(): void
    {
        $this->commissionRateProvider = $this->createMock(CommissionRateProvider::class);
        $this->exchangeRateProvider = $this->createMock(ExchangeRateProviderInterface::class);
        $this->commissionService = new CommissionService($this->commissionRateProvider, $this->exchangeRateProvider);
    }

    #[DataProvider('getEURTransactions')]
    public function testCalculateCommissionFeeForTransactionInEURAndEUCard(Transaction $transaction, string $expected)
    {
        $this->exchangeRateProvider->expects($this->never())->method('getRate');
        $this->commissionRateProvider->method('getRate')->with($transaction)->willReturn(CommissionRateProvider::RATE_EU);

        $this->assertEquals($expected, $this->commissionService->calculateCommissionFee($transaction));
    }

    #[DataProvider('getDataForNonEURTransactions')]
    public function testCalculateCommissionFeeForNonEURTransactions(Transaction $transaction, float $exchangeRate, string $expected)
    {
        $this->exchangeRateProvider->method('getRate')->with($transaction->currency)->willReturn($exchangeRate);
        $this->commissionRateProvider->method('getRate')->with($transaction)->willReturn(CommissionRateProvider::RATE_EU);

        $this->assertEquals($expected, $this->commissionService->calculateCommissionFee($transaction));
    }

    public static function getEURTransactions(): array
    {
        return [
            [new Transaction('123456', '100.00', 'EUR'), '1.00'],
            [new Transaction('123456', '99.99', 'EUR'), '1.00'],
            [new Transaction('123456', '64.99', 'EUR'), '0.65'],
        ];
    }

    public static function getDataForNonEURTransactions(): array
    {
        return [
            [new Transaction('123456', '50.00', 'USD'), 1.089208, '0.46'],
            [new Transaction('123456', '10000.00', 'JPY'), 144.643601, '0.70'],
            [new Transaction('123456', '130.00', 'USD'), 1.089208, '1.20'],
            [new Transaction('123456', '2000.00', 'GBP'), 0.877934, '22.79'],
        ];
    }
}
