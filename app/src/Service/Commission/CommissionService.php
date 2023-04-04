<?php

declare(strict_types=1);

namespace App\Service\Commission;

use App\Dto\Transaction;
use App\Service\Currency\ExchangeRate\ExchangeRateProviderInterface;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class CommissionService
{
    public const CURRENCY_EUR = 'EUR';

    private CommissionRateProvider $commissionRateProvider;
    private ExchangeRateProviderInterface $exchangeRateProvider;

    public function __construct(CommissionRateProvider $commissionRateProvider, ExchangeRateProviderInterface $exchangeRateProvider)
    {
        $this->commissionRateProvider = $commissionRateProvider;
        $this->exchangeRateProvider = $exchangeRateProvider;
    }

    public function calculateCommissionFee(Transaction $transaction): string
    {
        $transactionAmountInEUR = $this->getTransactionAmountInEUR($transaction);
        $commissionRate = $this->commissionRateProvider->getRate($transaction);

        $fee = $transactionAmountInEUR->multiply($commissionRate, Money::ROUND_UP);

        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);

        return $moneyFormatter->format($fee);
    }

    private function getTransactionAmountInEUR(Transaction $transaction): Money
    {
        if ($transaction->currency == self::CURRENCY_EUR) {
            return $this->createMoneyObject($transaction);
        }

        $rate = $this->exchangeRateProvider->getRate($transaction->currency);

        $amount = $this->createMoneyObject($transaction);

        return $amount->divide($rate, Money::ROUND_UP);
    }

    private function createMoneyObject(Transaction $transaction): Money
    {
        $currencies = new ISOCurrencies();
        $moneyParser = new DecimalMoneyParser($currencies);

        return $moneyParser->parse($transaction->amount, new Currency(self::CURRENCY_EUR));
    }
}
