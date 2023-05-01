<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sent_user_id',
        'receive_user_id',
        'transferred_amount'
    ];

    public function sentUser()
    {
        return $this->belongsTo(User::class, 'sent_user_id');
    }

    public function receiveUser()
    {
        return $this->belongsTo(User::class, 'receive_user_id');
    }
}
