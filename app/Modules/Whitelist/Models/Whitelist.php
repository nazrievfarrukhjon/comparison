<?php

namespace App\Modules\Whitelist\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Whitelist extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_uuid',
        'concat_names',
        'first_name',
        'second_name',
        'third_name',
        'fourth_name',
        'birth_date',
    ];
}
