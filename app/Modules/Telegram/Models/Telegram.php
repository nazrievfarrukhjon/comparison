<?php

namespace App\Modules\Telegram;

use Illuminate\Database\Eloquent\Model;

class Telegram extends Model
{
    protected $fillable = [
        'body',
        'operation',
    ];
}
