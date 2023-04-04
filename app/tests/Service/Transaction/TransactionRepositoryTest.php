<?php

declare(strict_types=1);

namespace App\Tests\Service\Transaction;

use App\Dto\Transaction;
use App\Dto\TransactionsList;
use App\Service\Transaction\TransactionRepository;
use PHPUnit\Framework\TestCase;

class TransactionRepositoryTest extends TestCase
{
    public function testCreateTransactionListFromFile(): void
    {
        $transactionRepository = new TransactionRepository(__DIR__.'/../../resources/input.txt');

        $expected = new TransactionsList();
        $expected->addTransaction(new Transaction('45717360', '100.00', 'EUR'));
        $expected->addTransaction(new Transaction('516793', '50.00', 'USD'));
        $expected->addTransaction(new Transaction('45417360', '10000.00', 'JPY'));
        $expected->addTransaction(new Transaction('41417360', '130.00', 'USD'));
        $expected->addTransaction(new Transaction('4745030', '2000.00', 'GBP'));

        $this->assertEquals($expected, $transactionRepository->getAll());
    }
}
