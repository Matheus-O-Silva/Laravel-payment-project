<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Transaction;

class TransactionPolicy
{
    public function sendMoney(User $user, Transaction $transaction)
    {
        return $user->hasPermissions(['send_money']);
    }

    public function receiveMoney(User $user, Transaction $transaction)
    {
        return $user->hasPermissions(['send_money','receive_money']);
    }
}
