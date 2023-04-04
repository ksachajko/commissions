<?php

declare(strict_types=1);

namespace App\Service\Transaction;

use App\Dto\Transaction;
use App\Dto\TransactionsList;

class TransactionRepository
{
    private string $filepath;

    public function __construct(string $filepath = __DIR__.'/../../../resources/input.txt')
    {
        $this->filepath = $filepath;
    }

    public function getAll(): TransactionsList
    {
        $transactions = new TransactionsList();

        $handle = fopen($this->filepath, 'r');

        while (($nextLine = fgets($handle)) !== false) {
            $decodedTransaction = json_decode($nextLine, false);

            $transactions->addTransaction(new Transaction(
                $decodedTransaction->bin,
                $decodedTransaction->amount,
                $decodedTransaction->currency,
            ));
        }

        fclose($handle);

        return $transactions;
    }
}
