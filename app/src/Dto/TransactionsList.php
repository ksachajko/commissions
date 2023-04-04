<?php

declare(strict_types=1);

namespace App\Dto;

class TransactionsList
{
    private array $transactions;

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    public function asArray(): array
    {
        return $this->transactions;
    }
}
